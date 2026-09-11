@extends(route_prefix().'admin.admin-master')

@section('title') {{__('All Widgets')}} @endsection

@section('style')
    <link rel="stylesheet" href="{{asset('assets/common/css/jquery-ui.min.css')}}">
    <link rel="stylesheet" href="{{global_asset('assets/new-landlord/css/tablar-icon.css')}}">
    <link href="{{ global_asset('assets/landlord/admin/css/nice-select.css') }}" rel="stylesheet">
@endsection

@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

<div class="tw-widget-builder">

    {{-- Page Header --}}
    <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden mb-6">
        <div class="px-4 sm:px-6 py-4 flex flex-wrap items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-widgets-outline text-primary text-base"></i>
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Widget Manager')}}</h3>
                <p class="text-xs text-muted">{{__('Drag widgets from the right panel into sidebar areas on the left')}}</p>
            </div>
        </div>
    </div>

    {{-- Two Column Layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        {{-- Left: Sidebar Widget Areas --}}
        <div class="lg:col-span-7">
            <div class="bg-surface rounded-xl shadow-main border border-main sticky top-4">
                <div class="px-4 sm:px-6 py-4 border-b border-main flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-info-soft flex items-center justify-center flex-shrink-0">
                        <i class="mdi mdi-page-layout-sidebar-left text-info text-sm"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-dark uppercase tracking-widest">{{__('Widget Areas')}}</h4>
                        <p class="text-[10px] text-muted">{{__('Expand an area and drag widgets into it')}}</p>
                    </div>
                </div>
                <div class="p-4 sm:p-5">
                    <div class="sidebar-list-wrap">
                        {!! get_admin_sidebar_list() !!}
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: Available Widgets --}}
        <div class="lg:col-span-5">
            <div class="bg-surface rounded-xl shadow-main border border-main sticky top-4">
                <div class="px-4 sm:px-6 py-4 border-b border-main flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-warning-soft flex items-center justify-center flex-shrink-0">
                        <i class="mdi mdi-puzzle-outline text-warning text-sm"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-dark uppercase tracking-widest">{{__('Available Widgets')}}</h4>
                        <p class="text-[10px] text-muted">{{__('Drag a widget to a sidebar area')}}</p>
                    </div>
                </div>

                {{-- Search --}}
                <div class="px-4 sm:px-5 pt-4">
                    <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                        <i class="mdi mdi-magnify text-lg text-primary"></i>
                        <input type="text" id="search_widget_field"
                               placeholder="{{__('Search widgets...')}}"
                               class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                    </div>
                </div>

                <div class="p-4 sm:p-5">
                    <ul id="sortable_02" class="available-form-field all-widgets sortable_02">
                        {!! render_admin_panel_widgets_list() !!}
                    </ul>
                </div>
            </div>
        </div>

    </div>

</div>

<x-media-upload.tw-markup/>

@endsection

@section('scripts')
    <script src="{{global_asset('assets/common/js/jquery-ui.min.js')}}"></script>
    @if(is_null(tenant()))
        {{-- tabler iconpicker loaded globally in landlord footer --}}
    @else
        <script src="{{global_asset('assets/common/js/tabler-iconpicker.js')}}"></script>
    @endif
    <script src="{{global_asset('assets/common/js/jquery.nice-select.min.js')}}"></script>

    <script>
        (function ($) {
            "use strict";

            $(document).ready(function () {

                // ── Nice Select ──────────────────────────────────────────
                $('.nice-select').niceSelect();

                // ── Repeater Script ──────────────────────────────────────
                $(document).on('click','.all-field-wrap .action-wrap .add',function (e){
                    e.preventDefault();

                    var el = $(this);
                    var parent = el.parent().parent();
                    var container = $('.all-field-wrap');
                    var clonedData = parent.clone();
                    var containerLength = container.length;
                    clonedData.find('#myTab').attr('id','mytab_'+containerLength);
                    clonedData.find('#myTabContent').attr('id','myTabContent_'+containerLength);
                    var allTab =  clonedData.find('.tab-pane');
                    allTab.each(function (index,value){
                        var el = $(this);
                        var oldId = el.attr('id');
                        el.attr('id',oldId+containerLength);
                    });
                    var allTabNav =  clonedData.find('.nav-link');
                    allTabNav.each(function (index,value){
                        var el = $(this);
                        var oldId = el.attr('href');
                        el.attr('href',oldId+containerLength);
                    });

                    parent.parent().append(clonedData);

                    if (containerLength > 0){
                        parent.parent().find('.remove').show(300);
                    }

                    // iconpicker re-init for cloned repeater items
                    parent.parent().find('.iconpicker-popover').remove();
                    parent.parent().find('.icp-dd').iconpicker('destroy');
                    parent.parent().find('.icp-dd').iconpicker();
                });

                $(document).on('click','.all-field-wrap .action-wrap .remove',function (e){
                    e.preventDefault();
                    var el = $(this);
                    var parent = el.parent().parent();
                    var container = $('.all-field-wrap');

                    if (container.length > 1){
                        el.show(300);
                        parent.hide(300);
                        parent.remove();
                    }else{
                        el.hide(300);
                    }
                });

                // ── Icon Picker ──────────────────────────────────────────
                $('.icp-dd').iconpicker();
                $('body').on('iconpickerSelected', '.icp-dd', function (e) {
                    var selectedIcon = e.iconpickerValue;
                    $(this).parent().parent().children('input').val(selectedIcon);
                });

                // ── Sortable (sidebar areas) ─────────────────────────────
                $(".sortable").sortable({
                    axis: "y",
                    placeholder: "sortable-placeholder",
                    receive : function(event,ui){
                        resetOrder(this.id);
                    },
                    stop: function( event, ui ){
                        resetOrder(this.id);
                    }
                }).disableSelection();

                // ── Sortable (available widgets → sidebar) ───────────────
                $(".sortable_02").sortable({
                    connectWith: '.sortable_widget_location',
                    helper: "clone",
                    remove: function (e, li) {
                        var Item = li.item.length > 0 ? li.item[0] : li.item;
                        var widgetName = Item.getAttribute('data-name');
                        var widgetNameSpace = Item.getAttribute('data-namespace');
                        var markup = '';
                        $.ajax({
                            'url' : "{{route(route_prefix().'admin.widgets.markup')}}",
                            'type' : "POST",
                            'data' : {
                                '_token' : "{!! csrf_token() !!}",
                                'widget_name' : widgetName,
                                'widget_namespace' : widgetNameSpace,
                            },
                            async: false,
                            success: function (data) {
                                markup = data;
                            }
                        });

                        li.item.clone()
                            .html(markup)
                            .insertAfter(li.item);
                        $(this).sortable('cancel');
                        return markup;
                    }
                }).disableSelection();

                // ── Remove Widget ────────────────────────────────────────
                $('body').on('click', '.remove-widget', function (e) {
                    var parent = $(this).parent();
                    Swal.fire({
                        title: "{{ __('Do you want to remove this widget?') }}",
                        text: "{{ __('If you remove this widget, all saved data associated with it will be lost.') }}",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonText: '{{ __("Remove") }}',
                        confirmButtonColor: '#dc2626',
                        cancelButtonColor: '#6b7280',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            parent.remove();
                            $(".sortable_02").sortable("refreshPositions");

                            var widgetType = parent.find('input[name="widget_type"]').val();
                            resetOrder();

                            if(widgetType === 'update') {
                                var widget_id = parent.find('input[name="id"]').val();
                                $.ajax({
                                    url: "{{route(route_prefix().'admin.widgets.delete')}}",
                                    type: "POST",
                                    data: {
                                        '_token': "{!! csrf_token() !!}",
                                        'id': widget_id
                                    },
                                    success: function (data) {
                                        toastr.success("{{ __('Widget removed successfully') }}");
                                    }
                                });
                            }
                        }
                    });
                });

                // ── Expand / Collapse Widget ─────────────────────────────
                $('body').on('click', '.expand', function (e) {
                    $(this).parent().find('.content-part').toggleClass('show');
                    var expand = $(this).children('i');
                    if(expand.hasClass('mdi-chevron-down')){
                        expand.attr('class', 'mdi mdi-chevron-up text-sm');
                    }else{
                        expand.attr('class', 'mdi mdi-chevron-down text-sm');
                    }
                    if ($.fn.iconpicker) {
                        $(this).parent().find('.icp-dd').iconpicker('destroy');
                        $(this).parent().find('.icp-dd').iconpicker();
                    }
                });

                // ── Save Widget ──────────────────────────────────────────
                $('body').on('click', '.widget_save_change_button', function (e) {
                    e.preventDefault();
                    var parent = $(this).parent().find('.widget_save_change_button');
                    parent.text('{{ __("Saving...") }}').attr('disabled',true);
                    var formClass =  $(this).parent();
                    var formData = formClass.serializeArray();
                    var widgetType = $(this).parent().find('input[name="widget_type"]').val();
                    var formAction = $(this).parent().attr('action');
                    var udpateId = '';
                    var formContainer = $(this).parent();

                    $.ajax({
                        type: "POST",
                        url:  formAction,
                        data: formClass.serializeArray() ,
                        success:function (data) {
                            udpateId = data.id;
                            if(widgetType == 'new'){
                                formContainer.attr('action',"{{route(route_prefix().'admin.widgets.update')}}")
                                formContainer.find('input[name="widget_type"]').val('update');
                                formContainer.prepend('<input type="hidden" name="id" value="'+udpateId+'">');
                            }
                        }
                    });

                    toastr.success('{{ __("Settings Saved") }}');
                    parent.text('{{ __("Saved") }}');
                    setTimeout(function () {
                        parent.text('{{ __("Save Changes") }}').attr('disabled',false);
                    },1000);
                });

                // ── Reset Order ──────────────────────────────────────────
                function resetOrder(dropedOn) {
                    var allItems = $('#'+dropedOn+' li');
                    $.each(allItems,function (index,value) {
                        $(this).find('input[name="widget_order"]').val(index+1);
                        $(this).find('input[name="widget_location"]').val(dropedOn);
                        var id = $(this).find('input[name="id"]').val();
                        var widget_order = index+1;
                        if(typeof id != 'undefined'){
                            reset_db_order(id,widget_order);
                        }
                    });
                }

                function reset_db_order(id,widget_order){
                    $.ajax({
                        type: "POST",
                        url:  "{{route(route_prefix().'admin.widgets.update.order')}}",
                        data: {
                            _token: "{{csrf_token()}}",
                            id : id,
                            widget_order: widget_order
                        },
                        success:function (data) {}
                    });
                }
            });

            // ── Widget Area Expand/Collapse ──────────────────────────
            $(document).on('click','.widget-area-expand',function (e) {
                e.preventDefault();
                var widgetbody = $(this).parent().parent().find('.widget-area-body');
                widgetbody.toggleClass('hide');
                var expand = $(this).children('i');
                if(expand.hasClass('mdi-chevron-down')){
                    expand.attr('class', 'mdi mdi-chevron-up text-sm');
                }else{
                    expand.attr('class', 'mdi mdi-chevron-down text-sm');
                    var allWidgets =  $(this).parent().parent().find('.widget-area-body ul li');
                    $.each(allWidgets,function (value){
                        $(this).find('.content-part').removeClass('show');
                    });
                }
            });

            // ── Search Widgets ───────────────────────────────────────
            $(document).on('keyup', '#search_widget_field', function () {
                var searchText = $(this).val().toLowerCase().trim();
                var allWidgets = $('#sortable_02 li > h4');

                $.each(allWidgets, function () {
                    var widgetTitle = $(this).text().toLowerCase();
                    if (widgetTitle.includes(searchText)) {
                        $(this).parent().show();
                    } else {
                        $(this).parent().hide();
                    }
                });
            });

        }(jQuery));
    </script>

    <x-media-upload.tw-js/>
@endsection
