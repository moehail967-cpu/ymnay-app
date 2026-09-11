@extends(route_prefix().'admin.admin-master')
@section('title') {{__('GDPR Compliance Settings')}} @endsection

@section('content')

<x-landlord-error-msg/>
<x-landlord-flash-msg/>

<form class="forms-sample" method="post" action="{{route(route_prefix().'admin.general.gdpr.settings')}}">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">

        {{-- Banner Content --}}
        <div class="lg:col-span-2 bg-surface rounded-xl shadow-main border border-main">
            <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-shield-check-outline text-primary text-base"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Cookie Banner Content')}}</h3>
                    <p class="text-xs text-muted">{{__('Text displayed in the GDPR cookie consent banner')}}</p>
                </div>
            </div>
            <div class="px-4 sm:px-6 py-5 space-y-5">
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('GDPR Title')}}</label>
                    <input type="text" name="site_gdpr_cookie_title" class="lnd-input"
                           value="{{get_static_option('site_gdpr_cookie_title')}}">
                </div>
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('GDPR Message')}}</label>
                    <textarea name="site_gdpr_cookie_message" class="lnd-input" rows="4">{{get_static_option('site_gdpr_cookie_message')}}</textarea>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('More Info Link Label')}}</label>
                        <input type="text" name="site_gdpr_cookie_more_info_label" class="lnd-input"
                               value="{{get_static_option('site_gdpr_cookie_more_info_label')}}">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('More Info Link')}}</label>
                        <input type="text" name="site_gdpr_cookie_more_info_link" class="lnd-input"
                               value="{{get_static_option('site_gdpr_cookie_more_info_link')}}">
                        <p class="text-[11px] text-muted mt-1.5">{{__('Use {url} for site address, e.g. {url}/about')}}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Toggle & Timing --}}
        <div class="bg-surface rounded-xl shadow-main border border-main h-fit lg:sticky lg:top-5">
            <div class="px-4 py-3.5 border-b border-main flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: var(--color-info-bg, #e0f2fe);">
                    <i class="mdi mdi-cog-outline text-sm" style="color: var(--color-info, #0ea5e9);"></i>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-dark font-urbanist">{{__('Cookie Settings')}}</h4>
                    <p class="text-[10px] text-muted">{{__('Status & timing')}}</p>
                </div>
            </div>
            <div class="divide-y divide-main">
                {{-- Enable/Disable --}}
                <div class="flex items-center justify-between px-4 py-4">
                    <div>
                        <span class="text-sm font-semibold text-dark">{{__('GDPR Cookie')}}</span>
                        <p class="text-[11px] text-muted">{{__('Enable consent banner')}}</p>
                    </div>
                    <label class="dr-toggle">
                        <input type="hidden" name="site_gdpr_cookie_enabled" value="">
                        <input type="checkbox" name="site_gdpr_cookie_enabled" value="on"
                            @checked(!empty(get_static_option('site_gdpr_cookie_enabled')))>
                        <span class="dr-toggle-track"></span>
                    </label>
                </div>

                {{-- Expire --}}
                <div class="px-4 py-4">
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Cookie Expire')}}</label>
                    <input type="text" name="site_gdpr_cookie_expire" class="lnd-input"
                           value="{{get_static_option('site_gdpr_cookie_expire')}}">
                    <p class="text-[11px] text-muted mt-1.5">{{__('Days until cookie expires (e.g. 30)')}}</p>
                </div>

                {{-- Delay --}}
                <div class="px-4 py-4">
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Show Delay')}}</label>
                    <input type="text" name="site_gdpr_cookie_delay" class="lnd-input"
                           value="{{get_static_option('site_gdpr_cookie_delay')}}">
                    <p class="text-[11px] text-muted mt-1.5">{{__('Milliseconds (e.g. 5000 = 5s)')}}</p>
                </div>
            </div>
        </div>

    </div>

    {{-- Button Labels --}}
    <div class="bg-surface rounded-xl shadow-main border border-main mb-5">
        <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-success-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-gesture-tap-button text-success text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Button Labels')}}</h3>
                <p class="text-xs text-muted">{{__('Customize button text on the cookie banner')}}</p>
            </div>
        </div>
        <div class="px-4 sm:px-6 py-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Accept Button')}}</label>
                    <input type="text" name="site_gdpr_cookie_accept_button_label" class="lnd-input"
                           value="{{get_static_option('site_gdpr_cookie_accept_button_label')}}">
                </div>
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Decline Button')}}</label>
                    <input type="text" name="site_gdpr_cookie_decline_button_label" class="lnd-input"
                           value="{{get_static_option('site_gdpr_cookie_decline_button_label')}}">
                </div>
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Manage Button')}}</label>
                    <input type="text" name="site_gdpr_cookie_manage_button_label" class="lnd-input"
                           value="{{get_static_option('site_gdpr_cookie_manage_button_label')}}">
                </div>
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Manage Title')}}</label>
                    <input type="text" name="site_gdpr_cookie_manage_title" class="lnd-input"
                           value="{{get_static_option('site_gdpr_cookie_manage_title')}}">
                </div>
            </div>
        </div>
    </div>

    {{-- Cookie Manage Items (Repeater) --}}
    <div class="bg-surface rounded-xl shadow-main border border-main mb-5">
        <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg flex items-center justify-center" style="background: var(--color-warning-bg, #fffbeb);">
                <i class="mdi mdi-cookie-outline text-base" style="color: var(--color-warning, #f59e0b);"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Cookie Manage Items')}}</h3>
                <p class="text-xs text-muted">{{__('Individual cookie categories users can toggle')}}</p>
            </div>
        </div>
        <div class="px-4 sm:px-6 py-5">
            @php
                $all_title_fields = get_static_option('site_gdpr_cookie_manage_item_title');
                $all_title_fields = !empty($all_title_fields) ? unserialize($all_title_fields, ['class' => false]) : [''];
                $all_description_fields = get_static_option('site_gdpr_cookie_manage_item_description');
                $all_description_fields = !empty($all_description_fields) ? unserialize($all_description_fields, ['class' => false]) : [''];
            @endphp

            <div class="iconbox-repeater-wrapper space-y-3">
                @foreach($all_title_fields as $index => $title)
                    <div class="all-field-wrap">
                        <div class="bg-secondary border border-main rounded-xl p-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Title')}}</label>
                                    <input type="text" name="site_gdpr_cookie_manage_item_title[]"
                                           class="lnd-input" value="{{$all_title_fields[$index] ?? ''}}">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Description')}}</label>
                                    <textarea name="site_gdpr_cookie_manage_item_description[]"
                                              class="lnd-input" rows="2">{{$all_description_fields[$index] ?? ''}}</textarea>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 mt-3 pt-3 border-t border-main">
                                <button type="button" class="add inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-primary text-white text-[10px] font-semibold hover:opacity-90 transition">
                                    <i class="mdi mdi-plus text-sm"></i> {{__('Add')}}
                                </button>
                                <button type="button" class="remove inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-white text-[10px] font-semibold hover:opacity-90 transition" style="background: var(--color-danger, #ef4444);">
                                    <i class="mdi mdi-trash-can-outline text-sm"></i> {{__('Remove')}}
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Save --}}
    <div class="flex items-center">
        <button type="submit"
                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
            <i class="mdi mdi-content-save-outline text-base"></i> {{__('Save Changes')}}
        </button>
    </div>

</form>

@endsection

@section('scripts')
    <script>
        $(document).on('click','.all-field-wrap .add',function (e){
            e.preventDefault();
            var el = $(this);
            var parent = el.closest('.all-field-wrap');
            var container = $('.all-field-wrap');
            var clonedData = parent.clone();
            clonedData.find('input[type="text"]').val('');
            clonedData.find('textarea').val('');
            parent.parent().append(clonedData);
            if (container.length > 0){
                parent.parent().find('.remove').show(300);
            }
        });

        $(document).on('click','.all-field-wrap .remove',function (e){
            e.preventDefault();
            var el = $(this);
            var parent = el.closest('.all-field-wrap');
            var container = $('.all-field-wrap');
            if (container.length > 1){
                parent.hide(300);
                parent.remove();
            }
        });
    </script>
@endsection
