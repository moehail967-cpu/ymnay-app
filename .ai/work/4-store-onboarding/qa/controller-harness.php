<?php
/**
 * Isolated control-flow regression harness for Issue #4.
 * Loads the exact, SHA-verified controller. Dependencies are deliberately in-memory
 * doubles. This is NOT Laravel/PHPUnit, database, lock, mail or end-to-end testing.
 * No network, secrets, databases, migrations or production writes are used.
 */
namespace QA {
    class HttpAbort extends \RuntimeException {}
    class State {
        public static array $rows = [], $events = [], $trials = [], $logs = [], $options = [], $optionReads = [];
        public static $eventHandler = null;
        public static ?\App\Models\User $user = null;
        public static int $init = 0, $end = 0;
        public static function reset(): void { self::$rows=[]; self::$events=[]; self::$trials=[]; self::$logs=[]; self::$options=[]; self::$optionReads=[]; self::$eventHandler=null; self::$user=null; self::$init=0; self::$end=0; }
    }
    class Record {
        public function __construct(public array $a = []) {}
        public function __get($k) { return $this->a[$k] ?? null; }
        public function __set($k,$v): void { $this->a[$k]=$v; }
        public function __isset($k): bool { return isset($this->a[$k]); }
        public static function create(array $a): static { $a['id'] ??= count(State::$rows[static::class] ?? [])+1; $r=new static($a); State::$rows[static::class][(string)$r->id]=$a; return $r; }
        public static function find($id): ?static { if ($id === null) return null; $a=State::$rows[static::class][(string)$id] ?? null; return $a ? new static($a) : null; }
        public static function findOrFail($id): static { return static::find($id) ?? throw new HttpAbort('Not found',404); }
        public static function query(): Query { return new Query(static::class); }
        public static function __callStatic($name,$args) { return static::query()->$name(...$args); }
        public function update(array $a): bool { $this->a=array_replace($this->a,$a); State::$rows[static::class][(string)$this->id]=array_replace(State::$rows[static::class][(string)$this->id]??[],$a); return true; }
        public function refresh(): static { $this->a=State::$rows[static::class][(string)$this->id]; return $this; }
        public function fresh(): static { return static::findOrFail($this->id); }
    }
    class Query {
        private array $filters=[];
        public function __construct(private string $cls) {}
        private function add(callable $f,string $op='and'): static { $this->filters[]=[$op,$f]; return $this; }
        private function make($key,$operator=null,$value=null): callable {
            if (is_callable($key)) { $q=new self($this->cls); $key($q); return fn($r)=>$q->matches($r); }
            if (func_num_args()===2) { $value=$operator; $operator='='; }
            return fn($r)=> match($operator) { '!=' => ($r[$key]??null)!=$value, default => ($r[$key]??null)==$value };
        }
        public function where(...$args): static { return $this->add($this->make(...$args)); }
        public function orWhere(...$args): static { return $this->add($this->make(...$args),'or'); }
        public function whereNull($k): static { return $this->add(fn($r)=>($r[$k]??null)===null); }
        public function whereIn($k,$values): static { return $this->add(fn($r)=>in_array($r[$k]??null,$values,true)); }
        public function whereKey($id): static { return $this->where('id',$id); }
        public function with(...$a): static { return $this; }
        public function latest(...$a): static { return $this; }
        public function orderBy(...$a): static { return $this; }
        public function lockForUpdate(): static { return $this; /* no DB locks: not a concurrency simulation */ }
        public function when($value,$callback): static { if($value) $callback($this,$value); return $this; }
        public function matches(array $r): bool { $ok=null; foreach($this->filters as [$op,$f]) { $b=$f($r); $ok=$ok===null?$b:($op==='or'?($ok||$b):($ok&&$b)); } return $ok??true; }
        public function get(): Collection { $c=$this->cls; return new Collection(array_map(fn($a)=>new $c($a),array_values(array_filter(State::$rows[$c]??[],fn($a)=>$this->matches($a))))); }
        public function first(): ?Record { return $this->get()->first(); }
        public function firstOrFail(): Record { return $this->first() ?? throw new HttpAbort('Not found',404); }
        public function find($id): ?Record { return $this->whereKey($id)->first(); }
        public function findOrFail($id): Record { return $this->whereKey($id)->firstOrFail(); }
        public function exists(): bool { return $this->first()!==null; }
    }
    class Collection {
        public function __construct(public array $items) {}
        public function where($k,$v): static { return new static(array_values(array_filter($this->items,fn($x)=>$x->$k==$v))); }
        public function pluck($k): static { return new static(array_map(fn($x)=>$x->$k,$this->items)); }
        public function all(): array { return $this->items; }
        public function contains($fn): bool { foreach($this->items as $x) if($fn($x)) return true; return false; }
        public function first() { return $this->items[0]??null; }
        public function firstWhere($k,$v) { return $this->where($k,$v)->first(); }
    }
    class Session {
        public function __construct(public array $data=[]) {}
        public function get($k) { return $this->data[$k]??null; }
        public function has($k): bool { return array_key_exists($k,$this->data); }
        public function put($k,$v): void { $this->data[$k]=$v; }
        public function forget($k): void { unset($this->data[$k]); }
    }
}
namespace App\Http\Controllers { class Controller {} }
namespace App\Models { class User extends \QA\Record {} class PricePlan extends \QA\Record {} class StoreOnboardingRequest extends \QA\Record {} class Tenant extends \QA\Record {} class PaymentLogs extends \QA\Record {} }
namespace Illuminate\Http {
    class JsonResponse { public function __construct(public array $data,public int $status=200) {} }
    class RedirectResponse { public array $data=[]; public function route($name,$args=[]): static { $this->data=[$name,$args]; return $this; } public function withErrors($errors): static { $this->data=$errors; return $this; } }
    class Request {
        public array $validations=[];
        public function __construct(public array $data=[],public ?\QA\Session $s=null,public array $cookies=[]) { $this->s??=new \QA\Session; }
        public function validate(array $rules,...$rest): array {
            $this->validations[]=$rules;
            foreach($rules as $key=>$list) foreach($list as $rule) {
                if($rule==='required' && (!isset($this->data[$key]) || $this->data[$key]==='')) throw new \QA\HttpAbort('required: '.$key,422);
                if($rule==='accepted' && !in_array($this->data[$key]??null,[true,1,'1','yes','on'],true)) throw new \QA\HttpAbort('accepted: '.$key,422);
            }
            return array_intersect_key($this->data,$rules);
        }
        public function session(): \QA\Session { return $this->s; }
        public function cookie($k) { return $this->cookies[$k]??null; }
        public function input($k) { return $this->data[$k]??null; }
        public function merge($d): void { $this->data=array_replace($this->data,$d); }
        public function isSecure(): bool { return false; }
        public function integer($k,$default=0): int { return (int)($this->data[$k]??$default); }
    }
}
namespace Illuminate\View { class View { public function __construct(public array $data=[]){} } }
namespace Illuminate\Support\Facades {
    class Auth { public static function guard($x): static { return new static; } public function user() { return \QA\State::$user; } public function check(): bool { return (bool)$this->user(); } public function id() { return $this->user()?->id; } }
    class Cookie { public static function queue(...$a): void {} public static function forget($x) { return $x; } }
    class DB { public static function transaction($f) { return $f(); } }
    class Log { public static function error($s,$data): void { \QA\State::$logs[]=$data; } }
}
namespace Illuminate\Support { class Str { public static function lower($s): string {return strtolower($s);} public static function limit($s,$len,$suffix=''): string {return substr($s,0,$len);} public static function uuid(): string {return 'qa-request-'.bin2hex(random_bytes(4));} } }
namespace Illuminate\Validation { class Rule { public static function __callStatic($n,$a): static {return new static;} public function __call($n,$a): static {return $this;} } }
namespace App\Events { class TenantRegisterEvent {public function __construct(public $user,public $subdomain,public $theme) {}} }
namespace App\Actions\Tenant {
    class TenantTrialPaymentLog {
        public static function trial_payment_log($user,$plan,$domain,$theme): bool {
            \QA\State::$trials[]=[$domain,$plan->id,$theme];
            \App\Models\PaymentLogs::create(['tenant_id'=>$domain,'user_id'=>$user->id,'status'=>'trial']); return true;
        }
    }
}
namespace {
    use QA\State; use App\Models\{User,PricePlan,Tenant,PaymentLogs,StoreOnboardingRequest}; use Illuminate\Http\Request;
    use App\Http\Controllers\Landlord\Frontend\StoreOnboardingController;
    function __($s) { return $s; }
    function abort_if($x,$code): void { if($x) throw new QA\HttpAbort('abort',$code); }
    function abort_unless($x,$code): void { abort_if(!$x,$code); }
    function response() { return new class { public function json($data,$status=200) { return new Illuminate\Http\JsonResponse($data,$status); } }; }
    function redirect() { return new Illuminate\Http\RedirectResponse; }
    function back() { return redirect(); }
    function view($n,$d) { return new Illuminate\View\View($d); }
    function collect($items) { return new QA\Collection($items); }
    function optional($x) { return new class { public function toISOString() { return null; } }; }
    function getAllThemeSlug() { return ['theme-a','theme-b']; }
    function getPricePlanBasedAllThemeData($slugs) { return array_map(fn($s)=>(object)['slug'=>$s],$slugs); }
    function get_static_option($key) { State::$optionReads[]=$key; return State::$options[$key]??''; }
    function update_static_option($key,$value) { State::$options[$key]=$value; return true; }
    function tenant_url_with_protocol($domain) { return 'https://'.$domain; }
    function now() { return '2026-09-15T01:00:00Z'; }
    function tenancy() { return new class { public function initialize($tenant): void { State::$init++; } public function end(): void { State::$end++; } }; }
    function event($e): void { State::$events[]=['subdomain'=>$e->subdomain,'theme'=>$e->theme]; (State::$eventHandler)($e); }

    $source = $argv[1] ?? __DIR__.'/StoreOnboardingController.php';
    $bytes=file_get_contents($source);
    $hash=sha1('blob '.strlen($bytes)."\0".$bytes);
    if($hash!=='ce4dc666de41d21266acbf549533b6ab36a30365') throw new RuntimeException('Unexpected source blob: '.$hash);
    require $source;
    function fixture(string $status='account_verified'): array {
        State::reset();
        $u=User::create(['id'=>7,'username'=>'qa-owner','email'=>'owner@example.invalid','email_verified'=>1]); State::$user=$u;
        $p=PricePlan::create(['id'=>1,'status'=>1,'title'=>'QA Plan','price'=>'30','type'=>0,'has_trial'=>true,'trial_days'=>60,'updated_at'=>null,'plan_themes'=>new QA\Collection([(object)['status'=>1,'theme_slug'=>'theme-a'],(object)['status'=>1,'theme_slug'=>'theme-b']])]);
        $c=new StoreOnboardingController;
        $snapshot=(new ReflectionMethod($c,'planSnapshot'))->invoke($c,$p);
        $o=StoreOnboardingRequest::create(['id'=>'qa-request','user_id'=>7,'plan_id'=>1,'theme_slug'=>'theme-a','store_name'=>'QA Store','subdomain'=>'qa-store','tenant_id'=>null,'status'=>$status,'plan_snapshot'=>$snapshot,'last_error'=>null]);
        $r=new Request(['terms_condition'=>true],new QA\Session(['store_onboarding_request_id'=>$o->id]));
        State::$eventHandler=function($e) { Tenant::create(['id'=>$e->subdomain,'user_id'=>$e->user->id,'domain'=>(object)['domain'=>'qa-store.example.invalid'],'unique_key'=>'test-only-fixture-key']); };
        return [$c,$r,$o,$p,$u];
    }
    $results=[];
    function runCase(string $id,string $name,callable $fn): void {
        global $results;
        try { [$ok,$observed]=$fn(); $results[]=['id'=>$id,'case'=>$name,'acceptance_result'=>$ok?'PASS':'FAIL','observed'=>$observed]; }
        catch(Throwable $e) { $results[]=['id'=>$id,'case'=>$name,'acceptance_result'=>'HARNESS_ERROR','observed'=>get_class($e).': '.$e->getMessage().' at '.$e->getLine()]; }
    }
    runCase('C01','Unverified account does not start provisioning',function(){[$c,$r,$o,$p,$u]=fixture();$u->email_verified=0;$x=$c->complete($r);return [$x->status===422 && !State::$events,['http'=>$x->status,'events'=>count(State::$events)]];});
    runCase('C02','Changed plan snapshot requires review',function(){[$c,$r,$o,$p]=fixture();$p->update(['price'=>'40']);$x=$c->complete($r);return [$x->status===409 && !State::$events,['http'=>$x->status,'status'=>$x->data['status']??null,'events'=>count(State::$events)]];});
    runCase('C03','Repeated completion of ready request avoids duplicate side effects',function(){[$c,$r]=fixture();$a=$c->complete($r);$b=$c->complete($r);return [($a->data['status']??null)==='ready'&&($b->data['status']??null)==='ready'&&count(State::$events)===1&&count(State::$trials)===1,['first'=>$a->data['status']??null,'second'=>$b->data['status']??null,'events'=>count(State::$events),'trial_calls'=>count(State::$trials)]];});
    runCase('C04','Another account cannot use the bound onboarding request',function(){[$c,$r]=fixture();State::$user=User::create(['id'=>9,'email_verified'=>1]);try{$c->complete($r);$status=200;}catch(QA\HttpAbort $e){$status=$e->getCode();}return [$status===404 && !State::$events,['http'=>$status,'events'=>count(State::$events)]];});
    runCase('C05','Trial on another store denies creation',function(){[$c,$r]=fixture();PaymentLogs::create(['user_id'=>7,'status'=>'trial','tenant_id'=>'other-store']);$x=$c->complete($r);return [$x->status===422&&!State::$events,['http'=>$x->status,'events'=>count(State::$events)]];});
    foreach(['selectPlan'=>['plan_id'=>1],'selectTheme'=>['theme_slug'=>'theme-b'],'storeDetails'=>['store_name'=>'Changed','subdomain'=>'changed-store']] as $method=>$input){runCase('C06-'.$method,'Provisioning request must not be mutable via '.$method,function()use($method,$input){[$c,$r,$o]=fixture('provisioning');$r->data=$input;$c->$method($r);$fresh=$o->fresh();return [$fresh->status==='provisioning',['before'=>'provisioning','after'=>$fresh->status,'theme'=>$fresh->theme_slug,'subdomain'=>$fresh->subdomain]];});}
    runCase('C07','Completed request must not revert to draft when selecting a theme',function(){[$c,$r,$o]=fixture();$c->complete($r);$r->data=['theme_slug'=>'theme-b'];$c->selectTheme($r);$fresh=$o->fresh();return [$fresh->status==='ready',['before'=>'ready','after'=>$fresh->status,'theme'=>$fresh->theme_slug,'tenant_id'=>$fresh->tenant_id]];});
    runCase('C08','Missing store details must be rejected before dispatch',function(){[$c,$r,$o]=fixture();$o->update(['store_name'=>null,'subdomain'=>null]);$x=$c->complete($r);return [!State::$events,['http'=>$x->status,'events'=>State::$events,'validator_fields'=>array_keys($r->validations[0])]];});
    runCase('C09','Partial creation before domain must resume the missing pipeline',function(){[$c,$r]=fixture();State::$eventHandler=function($e){Tenant::create(['id'=>$e->subdomain,'user_id'=>7,'domain'=>null,'unique_key'=>null]);throw new RuntimeException('Synthetic failure before domain job');};$a=$c->complete($r);State::$eventHandler=function($e){Tenant::findOrFail($e->subdomain)->update(['domain'=>(object)['domain'=>'qa-store.example.invalid'],'unique_key'=>'test-only-fixture-key']);};$b=$c->complete($r);$d=$c->complete($r);return [($d->data['status']??null)==='ready',['attempts'=>[$a->status,$b->status,$d->status],'states'=>[$a->data['status']??null,$b->data['status']??null,$d->data['status']??null],'pipeline_dispatches'=>count(State::$events),'trial_calls'=>count(State::$trials),'last_error'=>StoreOnboardingRequest::find('qa-request')->last_error]];});
    runCase('C10','Final creation must honor newly reserved subdomain',function(){[$c,$r]=fixture();State::$options['forbidden_subdomains']='qa-store';$x=$c->complete($r);return [!State::$events,['http'=>$x->status,'status'=>$x->data['status']??null,'events'=>count(State::$events),'forbidden_option_read'=>in_array('forbidden_subdomains',State::$optionReads,true)]];});
    $out=['candidate'=>'b7d0a52ef23e6b1620509c49e62f1ef86f4bf70b','source_blob'=>$hash,'php'=>PHP_VERSION,'method'=>'Actual controller with in-memory dependency doubles; not Laravel/DB/E2E/locking coverage','results'=>$results];
    echo json_encode($out,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE)."\n";
    exit(count(array_filter($results,fn($r)=>$r['acceptance_result']!=='PASS'))?1:0);
}
