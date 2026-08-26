@extends('landlord.admin.admin-master')

@section('title')
    {{__('Contact Form Builder')}}
@endsection

@section('style')
    <link rel="stylesheet" href="{{global_asset('assets/plugins/PageBuilder/css/jquery-ui.min.css')}}">
@endsection

@section('content')

<x-landlord-error-msg/>
<x-landlord-flash-msg/>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

    {{-- Form Builder (Left) --}}
    <div class="lg:col-span-9">
        <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">
            <div class="px-4 sm:px-6 py-4 border-b border-main flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                    <i class="las la-wpforms text-primary text-base"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Contact Form Builder')}}</h3>
                    <p class="text-xs text-muted">{{__('Drag fields from the right panel to build your form')}}</p>
                </div>
            </div>
            <div class="p-4 sm:p-6">
                <form action="{{route('admin.form.builder.contact')}}" method="POST">
                    @csrf
                    {!! render_tw_drag_drop_form_builder_markup(get_static_option('contact_page_contact_form_fields')) !!}
                    <div class="pt-4 mt-4 border-t border-main">
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                            <i class="las la-save"></i> {{__('Save Changes')}}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Available Fields (Right Sidebar) --}}
    <div class="lg:col-span-3">
        <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden sticky top-4">
            <div class="px-4 py-4 border-b border-main">
                <h4 class="text-xs font-bold text-dark uppercase tracking-widest">{{__('Available Fields')}}</h4>
            </div>
            <div class="p-4">
                <ul id="sortable_02" class="fb-available-list" style="grid-template-columns: 1fr;">
                    <li class="fb-available-item" type="text"><i class="las la-font"></i> {{__('Text')}}</li>
                    <li class="fb-available-item" type="email"><i class="las la-envelope"></i> {{__('Email')}}</li>
                    <li class="fb-available-item" type="tel"><i class="las la-phone"></i> {{__('Tel')}}</li>
                    <li class="fb-available-item" type="url"><i class="las la-link"></i> {{__('URL')}}</li>
                    <li class="fb-available-item" type="select"><i class="las la-list"></i> {{__('Select')}}</li>
                    <li class="fb-available-item" type="checkbox"><i class="las la-check-square"></i> {{__('Check Box')}}</li>
                    <li class="fb-available-item" type="file"><i class="las la-file-upload"></i> {{__('File')}}</li>
                    <li class="fb-available-item" type="textarea"><i class="las la-align-left"></i> {{__('Textarea')}}</li>
                </ul>
            </div>
        </div>
    </div>

</div>

@endsection

@section('scripts')
    <script src="{{global_asset('assets/plugins/PageBuilder/js/jquery-ui.min.js')}}"></script>
    <script>
        (function ($) {
            "use strict";
            $(document).ready(function () {
                $("#sortable").sortable({
                    axis: "y",
                    handle: ".fb-drag-handle",
                    placeholder: "sortable-placeholder",
                    out: function(event, ui) {
                        setTimeout(function(){
                            var allShortableList = $("#sortable li");
                            allShortableList.each(function (index, value) {
                                var el = $(this);
                                el.find('.field-required').attr('name', 'field_required['+index+']');
                                el.find('.mime-type').attr('name', 'mimes_type['+index+']');
                            });
                        }, 500);
                    }
                }).disableSelection();

                $("#sortable_02").sortable({
                    connectWith: '#sortable',
                    helper: "clone",
                    remove: function (e, li) {
                        var value = li.item.prop('type') || li.item.attr('type');
                        var random = Math.floor(Math.random(9999) * 999999);
                        var formFiledLength = $('#sortable li').length - 1;
                        var markup = render_drag_drop_form_field_markup(value, random, formFiledLength);
                        li.item.clone()
                            .prop('id', value + '_' + random)
                            .text('')
                            .removeClass('fb-available-item')
                            .addClass('fb-field-item is-open')
                            .append(markup)
                            .insertAfter(li.item);
                        $(this).sortable('cancel');
                        return li.item.clone();
                    }
                }).disableSelection();

                $(document).on('input', '.field-placeholder', function (e) {
                    $(this).closest('.fb-field-body').prev('.fb-field-header').find('.fb-placeholder-name').text($(this).val());
                });

                $('body').on('click', '.remove-fields', function (e) {
                    e.stopPropagation();
                    $(this).closest('.fb-field-item').remove();
                    $("#sortable").sortable("refreshPositions");
                });

                function render_drag_drop_form_field_markup(type, random, formFiledLength) {
                    var markup = '';
                    markup += '<div class="fb-field-header" onclick="this.parentElement.classList.toggle(\'is-open\')">' +
                        '<i class="las la-grip-vertical fb-drag-handle"></i>' +
                        '<span class="fb-type-label">' + type + ': <span class="fb-placeholder-name"></span></span>' +
                        '<span class="fb-remove-btn remove-fields" onclick="event.stopPropagation();"><i class="las la-times"></i></span>' +
                        '<i class="las la-chevron-down fb-toggle-icon"></i>' +
                        '</div>' +
                        '<div class="fb-field-body">' +
                        '<input type="hidden" name="field_type[]" value="' + type + '">' +
                        '<div class="mb-3">' +
                        '<label class="lnd-label">{{ __('Name') }}</label>' +
                        '<input type="text" class="lnd-input" name="field_name[]" placeholder="{{ __('enter field name') }}">' +
                        '</div>' +
                        '<div class="mb-3">' +
                        '<label class="lnd-label">{{ __('Placeholder/Label') }}</label>' +
                        '<input type="text" class="lnd-input field-placeholder" name="field_placeholder[]" placeholder="{{ __('enter field placeholder') }}">' +
                        '</div>' +
                        '<div class="mb-3">' +
                        '<div class="fb-toggle-switch">' +
                        '<input type="checkbox" class="field-required" name="field_required[' + formFiledLength + ']">' +
                        '<span class="fb-toggle-label">{{ __('Required') }}</span>' +
                        '</div>' +
                        '</div>';

                    if (type === 'select') {
                        markup += '<div class="mb-3">' +
                            '<label class="lnd-label">{{ __('Options') }}</label>' +
                            '<textarea name="select_options[]" class="lnd-input" style="max-height:120px" cols="30" rows="5"></textarea>' +
                            '<p class="text-xs text-muted mt-1">separate option by new line</p>' +
                            '</div>';
                    }
                    if (type === 'file') {
                        markup += '<div class="mb-3">' +
                            '<label class="lnd-label">{{ __('File Type') }}</label>' +
                            '<select name="mimes_type[' + formFiledLength + ']" class="lnd-input mime-type">' +
                            '<option value="mimes:jpg,jpeg,png">jpg,jpeg,png</option>' +
                            '<option value="mimes:txt,pdf">txt,pdf</option>' +
                            '<option value="mimes:doc,docx">doc,docx</option>' +
                            '<option value="mimes:doc,docx,jpg,jpeg,png,txt,pdf">doc,docx,jpg,jpeg,png,txt,pdf</option>' +
                            '</select>' +
                            '</div>';
                    }

                    markup += '</div>';
                    return markup;
                }
            });
        }(jQuery));
    </script>
@endsection
