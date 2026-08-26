<?php

namespace Modules\Blog\Http\Controllers\Landlord\Admin;

use App\Actions\Blog\BlogAction;
use App\Enums\SlugMorphableTypeEnum;
use App\Facades\GlobalLanguage;
use App\Helpers\DataTableHelpers\General;
use App\Helpers\FlashMsg;
use App\Helpers\LanguageHelper;
use App\Helpers\ResponseMessage;
use App\Http\Requests\BlogInsertRequest;
use App\Http\Requests\BlogUpdateRequest;
use App\Http\Services\DynamicCustomSlugValidation;
use App\Models\Language;
use App\Models\MetaInfo;
use App\Models\Page;
use App\Models\Themes;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Modules\Blog\Entities\Blog;
use Modules\Blog\Entities\BlogCategory;
use Modules\Blog\Entities\BlogComment;
use Yajra\DataTables\DataTables;

class BlogController extends Controller
{

    private const BASE_PATH = 'blog::landlord.admin.blog.';

    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('permission:blog-list|blog-edit|blog-delete',['only' => ['index']]);
        $this->middleware('permission:blog-create',['only' => ['new_blog','store_new_blog']]);
        $this->middleware('permission:blog-edit',['only' => ['clone_blog','edit_blog','update_blog']]);
        $this->middleware('permission:blog-delete',['only' => ['delete_blog','bulk_action_blog','delete_blog_all_lang']]);
        $this->middleware('permission:blog-settings',['only' => ['blog_single_page_settings','update_blog_single_page_settings']]);
        $this->middleware('permission:page-settings-blog-page-manage',['only' => ['blog_area','update_blog_area']]);
    }

    public function index(Request $request){
        $default_lang = $request->lang ?? GlobalLanguage::default_slug();

        if ($request->ajax()){
            $data = Blog::select('*')->orderBy('id','desc')->get();

            return DataTables::of($data)
                ->addIndexColumn()

                ->addColumn('checkbox',function ($row){
                    return General::bulkCheckbox($row->id);
                })

                ->editColumn('id', function($row){
                    return '<span class="text-[11px] font-bold text-primary"># '.$row->id.'</span>';
                })

                ->addColumn('title_info',function ($row){
                    $title = e(\Str::words($row->title, 10));
                    $views = $row->views ?? 0;

                    if ($row->status == 1) {
                        $statusBadge = '<span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded bg-success-soft text-success text-[10px] font-bold uppercase"><i class="mdi mdi-check-circle text-[10px]"></i> '.__('Published').'</span>';
                    } else {
                        $statusBadge = '<span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded bg-warning-soft text-warning text-[10px] font-bold uppercase"><i class="mdi mdi-pencil-outline text-[10px]"></i> '.__('Draft').'</span>';
                    }

                    return '<div class="flex flex-col gap-1"><span class="text-sm font-semibold text-dark">'.$title.'</span><div class="flex items-center gap-2 flex-wrap">'.$statusBadge.'<span class="text-[10px] text-muted"><i class="mdi mdi-eye-outline"></i> '.$views.' '.__('views').'</span></div></div>';
                })

                ->addColumn('image',function ($row) {
                    $img = General::image($row->image);
                    if (!empty($img)) {
                        return '<div class="w-14 h-14 rounded-lg overflow-hidden border border-main flex-shrink-0"><img src="'.get_attachment_image_by_id($row->image, null, true)['img_url'].'" alt="" class="w-full h-full object-cover"></div>';
                    }
                    return '<div class="w-14 h-14 rounded-lg bg-secondary border border-main flex items-center justify-center"><i class="mdi mdi-image-outline text-muted text-lg"></i></div>';
                })

                ->addColumn('category_id',function ($row){
                    $catTitle = optional($row->category)->title;
                    if ($catTitle) {
                        return '<span class="inline-flex items-center px-2 py-0.5 rounded bg-primary-soft text-primary text-[10px] font-bold">'.e($catTitle).'</span>';
                    }
                    return '<span class="text-xs text-muted">—</span>';
                })

                ->addColumn('date',function ($row){
                    return '<span class="text-xs text-muted">'.$row->created_at->format('D, d-m-y').'</span>';
                })

                ->addColumn('action', function($row) use($default_lang){
                    $admin = auth()->guard('admin')->user();

                    $action = '<div class="flex items-center justify-end"><div class="row-action-wrap">';

                    // Edit button (always visible)
                    if ($admin->can('blog-edit')) {
                        $action .= General::twEditBtn(route(route_prefix().'admin.blog.edit', $row->id).'?lang='.$default_lang);
                    }

                    // View button (always visible)
                    $action .= General::twViewBtn(dynamicRoute($row->slug));

                    // Dropdown trigger
                    $action .= General::twDropdownTrigger();

                    // Dropdown menu
                    $action .= General::twDropdownStart();

                    if ($admin->can('blog-edit')) {
                        $action .= General::twDropdownFormBtn(
                            route(route_prefix().'admin.blog.clone'),
                            'item_id', $row->id,
                            'mdi-content-copy', 'bg-[#f3e8ff]', 'text-[#9333ea]',
                            __('Clone to Draft')
                        );
                    }

                    $commentsCount = \Modules\Blog\Entities\BlogComment::where('blog_id', $row->id)->count();
                    $action .= General::twDropdownLink(
                        route(route_prefix().'admin.blog.comments.view', $row->id),
                        'mdi-comment-processing-outline', 'bg-success-soft', 'text-success',
                        __('Comments').' ('.$commentsCount.')'
                    );

                    if ($admin->can('blog-delete')) {
                        $action .= General::twDropdownDivider();
                        $action .= General::twDropdownDeleteBtn();
                    }

                    $action .= General::twDropdownEnd();
                    $action .= '</div>'; // close row-action-wrap

                    // Hidden delete form (outside row-action-wrap)
                    if ($admin->can('blog-delete')) {
                        $action .= General::twDeleteForm(route(route_prefix().'admin.blog.delete.all.lang', $row->id));
                    }

                    $action .= '</div>'; // close flex container

                    return $action;
                })
                ->rawColumns(['action','checkbox','image','category_id','title_info','id','date'])
                ->make(true);
        }

        return view(self::BASE_PATH.'index',compact('default_lang'));
    }
    public function new_blog(Request $request)
    {
        $all_category = BlogCategory::where('status',1)->get();

        return view(self::BASE_PATH.'blog-new')->with([
            'all_blog_category' => $all_category,
            'default_lang' => $request->lang ?? GlobalLanguage::default_slug(),
        ]);
    }
    public function store_new_blog(BlogInsertRequest $request, BlogAction $blogAction)
    {
        $validatedData = $request->validated();
        DynamicCustomSlugValidation::validate(
            slug: $validatedData['slug'] ?? Str::slug($validatedData['title'], '-', null),
        );

        if(tenant()) {
            $blog_count = Blog::count();
            $blog_limit = tenant()?->payment_log?->package?->blog_permission_feature;
            $blog_limit = $blog_limit === -1 ? $blog_count + 1 : $blog_limit;

            if ($blog_count >= $blog_limit)
            {
                return back()->with(FlashMsg::explain('danger',__('You can not upload more blogs due to your blog upload limit!')));
            }
        }

        $blogAction->store_execute($request);
        return response()->success(ResponseMessage::SettingsSaved());
    }

    public function edit_blog(Request $request,$id){

        if(!empty($id)){
            $blog_post = Blog::find($id);
             $all_category = BlogCategory::select(['id','title'])->get();
        }

        return view(self::BASE_PATH.'blog-edit')->with([
            'all_blog_category' => $all_category,
            'blog_post' => $blog_post,
            'default_lang' => $request->lang ?? GlobalLanguage::default_slug(),
        ]);
    }

    public function update_blog(BlogUpdateRequest $request, BlogAction $blogAction,$id)
    {
        $validatedData = $request->validated();
        DynamicCustomSlugValidation::validate(
            slug: $validatedData['slug'] ?? Str::slug($validatedData['title'], '-', null),
            id: $id,
            type: SlugMorphableTypeEnum::BLOG
        );

        $blogAction->update_execute($request,$id);
        return response()->success(ResponseMessage::SettingsSaved());
    }

    public function delete_blog_all_lang($id){
        $blog = Blog::find($id);
        $blog->metainfo()->delete();
        $blog->delete();
        return response()->danger(ResponseMessage::delete());
    }

    public function bulk_action_blog(Request $request){
         Blog::whereIn('id',$request->ids)->delete();
         MetaInfo::whereIn('metainfoable_id',$request->ids)->delete();
        return response()->json(['status' => 'ok']);
    }

    public function clone_blog(Request $request, BlogAction $blogAction)
    {
        if(tenant()) {
            $blog_count = Blog::count();
            $blog_limit = tenant()?->payment_log?->package?->blog_permission_feature;

            if ($blog_count >= $blog_limit)
            {
                return back()->with(FlashMsg::explain('danger','You can not upload more blogs due to your blog upload limit!'));
            }
        }

        $blogAction->clone_blog_execute($request);
        return response()->success(ResponseMessage::SettingsSaved('Blog Cloned Successfylly..'));
    }


    public function view_comments($id)
    {
        $blog_comments = Blog::with('comments')->find($id);
        BlogComment::where('blog_id',$blog_comments->id)->update(['status' => 'read']);

        return view(self::BASE_PATH.'comments',compact('blog_comments'));
    }

    public function delete_all_comments(Request $request,$id){
        $category =  BlogComment::where('id',$id)->first();
        $category->delete();
        return response()->danger(ResponseMessage::delete());
    }

    public function bulk_action_comments(Request $request){
        BlogComment::whereIn('id',$request->ids)->delete();
        return response()->json(['status' => 'ok']);
    }

    public function blog_settings()
    {
        return view(self::BASE_PATH.'settings');
    }

    public function update_blog_settings(Request $request)
    {
            $request->validate([
                'category_page_item_show' => 'nullable|string',
                'tag_page_item_show' => 'nullable|string',
                'search_page_item_show' => 'nullable|string',
                'blogs_page_item_show' => 'nullable|string',
                'blog_avatar_image' => 'nullable|string',
            ]);
            $fields = [
                'category_page_item_show',
                'tag_page_item_show',
                'search_page_item_show',
                'blogs_page_item_show',
                'blog_avatar_image',
            ];
            foreach ($fields as $item) {
              if($request->has($item)){
               update_static_option($item, $request->$item);
            }

        }
        return response()->success(ResponseMessage::SettingsSaved());
    }
}
