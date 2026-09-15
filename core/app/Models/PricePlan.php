<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Stancl\Tenancy\Contracts\SyncMaster;
use Stancl\Tenancy\Database\Concerns\CentralConnection;
use Stancl\Tenancy\Database\Concerns\ResourceSyncing;

class PricePlan extends Model
{
    use HasFactory;

    protected $fillable = ['title','features','type','status','price','free_trial','faq',
        'page_permission_feature','blog_permission_feature','product_permission_feature','storage_permission_feature', 'package_badge', 'package_description',
        'razorpay_plan_id','razorpay_synced_at'];

    protected $casts = [
        'type' => 'integer',
        'status' => 'integer',
        'razorpay_synced_at' => 'datetime'
    ];

    /**
     * Price-plan titles exist as both legacy plain text and translation JSON.
     * Keep this model independent from the optional translatable package so
     * the core onboarding fixture and the full application use one contract.
     */
    public function getTranslation(string $key, string $locale): mixed
    {
        $value = $this->getRawOriginal($key);
        if (! is_string($value) || $value === '') {
            return $value;
        }

        $translations = json_decode($value, true);
        if (! is_array($translations)) {
            return $value;
        }

        return $translations[$locale]
            ?? $translations[config('app.fallback_locale')]
            ?? reset($translations);
    }

    public function getTitleAttribute(mixed $value): mixed
    {
        return $this->getTranslation('title', app()->getLocale());
    }

    public function plan_features()
    {
        return $this->hasMany(PlanFeature::class,'plan_id','id');
    }

    public function plan_themes()
    {
        return $this->hasMany(PlanTheme::class,'plan_id','id');
    }

    public function plan_payment_gateways()
    {
        return $this->hasMany(PlanPaymentGateway::class,'plan_id','id');
    }
}
