<?php


namespace App\Helpers\DataTableHelpers;


use App\BlogComment;
use App\Helpers\LanguageHelper;
use http\Env\Request;
use App\Blog;
use App\Helpers\FlashMsg;

class General
{
    public static function bulkCheckbox($id){
        return <<<HTML
<div class="bulk-checkbox-wrapper">
    <input type="checkbox" class="bulk-checkbox" name="bulk_delete[]" value="{$id}">
</div>
HTML;

    }

    public static function image($image_id){
        return render_attachment_preview_for_admin($image_id);
    }

    public static function deletePopover($url){
            $title = 'Delete';
            $token = csrf_token();
            return <<<HTML
<a tabindex="0" class="btn btn-danger btn-xs mb-3 mr-1 swal_delete_button"
 data-bs-toggle="tooltip" data-bs-placement="top" title="{$title}"
>
    <i class="la la-trash"></i>
</a>
<form method='post' action='{$url}' class="d-none">
<input type='hidden' name='_token' value='{$token}'>
<br>
<button type="submit" class="swal_form_submit_btn d-none"></button>
 </form>
HTML;

    }

    public static function editIcon($url){
        $title = 'Edit';
        return <<<HTML
<a class="btn btn-primary btn-xs mb-3 mr-1" href="{$url}"
 data-bs-toggle="tooltip" data-bs-placement="top" title="Edit"
>
    <i class="la la-edit"></i>
</a>
HTML;

    }

    public static function viewIcon($url){
        $title = __('View');
        return <<<HTML
<a class="btn btn-info btn-xs mb-3 mr-1" target="_blank" href="{$url}"
data-bs-toggle="tooltip" data-bs-placement="top" title="{$title}"
>
    <i class="la la-eye"></i>
</a>
HTML;

    }


    public static function viewAnalytics($url){

        $test = __('View Analytics');
        return <<<HTML
<a class="btn btn-warning btn-sm mb-3 mr-1" href="{$url}">
    {$test}
</a>
HTML;

}

    public static function viewComments($route,$id){

        $commentsCOunt = \Modules\Blog\Entities\BlogComment::where('blog_id',$id)->count();

        $test = '<strong class="text-white"> ('.$commentsCOunt.') </strong>';
        return <<<HTML
        <a class="btn btn-success btn-sm mb-3 mr-1" href="{$route}">
        <i class="mdi mdi-comment-processing" style="vertical-align: middle"></i>
            {$test}
        </a>
HTML;

}

    public static function blogApprove($id){
        $title = __('Approve This Blog');
        $csrf = csrf_field();
        $action = route('admin.blog.approve');
        return <<<HTML
    <form action="{$action}" method="post"
          enctype="multipart/form-data">
          {$csrf}
        <input type="hidden" name="id" value="{$id}">
        <button class="btn btn-info text-white mb-2"
                type="submit">{$title}
        </button>
    </form>
HTML;

    }

    public static function cloneIcon($action,$id){
        $title = 'clone this to new draft';
        $csrf = csrf_field();
        return <<<HTML
<form action="{$action}" method="post" class="d-inline">
{$csrf}
    <input type="hidden" name="item_id" value="{$id}">
    <button type="submit" data-bs-toggle="tooltip" title="{$title}" class="btn btn-xs btn-secondary btn-sm mb-3 mr-1"><i class="la la-copy"></i></button>
</form>
HTML;

    }

    public static function statusSpan($status){
        $output = '';
        if($status === 0){
            $output .= '<span class="alert alert-warning" >'.__('Draft').'</span>';
        }elseif($status === 1){
            $output .= '<span class="alert alert-success" >'.__('Published').'</span>';
        }
        return $output;
    }

    public static function paymentAccept($url){
        $token = csrf_token();
        return <<<HTML
<a tabindex="0" class="btn btn-success btn-xs mb-3 mr-1 swal_change_approve_payment_button">
    <i class="ti-check"></i>
</a>
<form method='post' action='{$url}' class="d-none">
    <input type='hidden' name='_token' value='{$token}'>
    <br>
    <button type="submit" class="swal_form_submit_btn d-none"></button>
</form>

HTML;

    }

    public static function invoiceBtn($url,$id){
        $csrf = csrf_field();
        $title = __('Invoice');
        return <<<HTML
 <form action="{$url}"  method="post">{$csrf}
    <input type="hidden" name="id" id="invoice_generate_order_field" value="{$id}">
    <button class="btn btn-secondary mb-2" type="submit">{$title}</button>
</form>
HTML;

    }

    public static function reminderMail($url,$id){
        $csrf = csrf_field();
        return <<<HTML
<form action="{$url}"  method="post">
{$csrf}
    <input type="hidden" name="id" value="{$id}">
    <button class="btn btn-secondary mb-2" type="submit"><i class="fas fa-bell"></i></button>
</form>
HTML;

    }

public static function anchor($url,$text,$class='primary'){
        return <<<HTML
<a class="btn btn-xs mb-3 mr-1 btn-{$class}" href="{$url}">{$text}</a>
HTML;
}



    // ── Tailwind-styled action helpers for new table UI ─────────────────

    public static function twEditBtn($url)
    {
        $title = __('Edit');
        return '<a href="'.$url.'" title="'.e($title).'" class="w-9 h-9 mr-1 rounded-lg bg-primary-soft border border-main flex items-center justify-center hover:text-white hover:bg-primary hover:border-primary transition-all"><i class="mdi mdi-pencil-outline text-sm"></i></a>';
    }

    public static function twViewBtn($url)
    {
        $title = __('View');
        return '<a href="'.$url.'" target="_blank" title="'.e($title).'" class="w-9 h-9 mr-1 rounded-lg bg-info-soft border border-main flex items-center justify-center hover:text-white hover:bg-info hover:border-info transition-all"><i class="mdi mdi-eye-outline text-sm"></i></a>';
    }

    public static function twDropdownTrigger()
    {
        return '<button type="button" onclick="toggleRowMenu(this)" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-secondary border border-main text-dark text-xs font-semibold hover:bg-primary-soft hover:text-primary hover:border-primary transition-all"><i class="mdi mdi-dots-vertical text-sm"></i></button>';
    }

    public static function twDropdownStart()
    {
        return '<div class="row-action-menu hidden">';
    }

    public static function twDropdownEnd()
    {
        return '</div>';
    }

    public static function twDropdownLink($url, $icon, $iconBg, $iconColor, $label, $target = '')
    {
        $targetAttr = $target ? " target=\"{$target}\"" : '';
        return "<a href=\"{$url}\"{$targetAttr}><span class=\"action-icon {$iconBg}\"><i class=\"mdi {$icon} {$iconColor}\"></i></span>{$label}</a>";
    }

    public static function twDropdownFormBtn($action, $hiddenName, $hiddenValue, $icon, $iconBg, $iconColor, $label)
    {
        $token = csrf_token();
        return "<form action=\"{$action}\" method=\"post\"><input type=\"hidden\" name=\"_token\" value=\"{$token}\"><input type=\"hidden\" name=\"{$hiddenName}\" value=\"{$hiddenValue}\"><button type=\"submit\" class=\"action-item\"><span class=\"action-icon {$iconBg}\"><i class=\"mdi {$icon} {$iconColor}\"></i></span>{$label}</button></form>";
    }

    public static function twDropdownDeleteBtn()
    {
        $label = __('Delete');
        return "<button type=\"button\" class=\"action-item action-danger swal_delete_button\"><span class=\"action-icon bg-danger-soft\"><i class=\"mdi mdi-delete-outline text-danger\"></i></span>{$label}</button>";
    }

    public static function twDropdownDivider()
    {
        return '<div class="menu-divider"></div>';
    }

    public static function twDeleteForm($url)
    {
        $token = csrf_token();
        return "<form method=\"post\" action=\"{$url}\" class=\"hidden d-none\"><input type=\"hidden\" name=\"_token\" value=\"{$token}\"><button type=\"submit\" class=\"swal_form_submit_btn hidden d-none\"></button></form>";
    }

}//end class
