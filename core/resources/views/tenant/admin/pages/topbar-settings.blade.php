@extends('tenant.admin.admin-master')
@section('title')
    {{__('Topbar Settings')}}
@endsection

@section('style')
    <style>.hover\:text-white:hover{color:#fff!important}</style>
@endsection

@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

<form method="post" action="{{route('tenant.admin.topbar.settings')}}">
    @csrf

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">

        {{-- Left Column: Main Settings --}}
        <div class="xl:col-span-8 space-y-6">

            {{-- Social Icons --}}
            <div class="bg-surface rounded-xl shadow-main border border-main">
                <div class="px-4 sm:px-6 py-4 border-b border-main flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-[rgba(252,79,0,0.1)] flex items-center justify-center flex-shrink-0">
                            <i class="mdi mdi-share-variant-outline text-[#FC4F00] text-base"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Social Icons')}}</h3>
                            <p class="text-xs text-muted">{{__('Links shown in topbar social area')}}</p>
                        </div>
                    </div>
                    <button type="button" id="open_add_social_modal"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-primary text-white text-xs font-bold hover:opacity-90 transition whitespace-nowrap">
                        <i class="mdi mdi-plus text-sm"></i> {{__('Add New')}}
                    </button>
                </div>

                <div class="p-4 sm:p-6">
                    @forelse($all_social_icons ?? [] as $data)
                        @if($loop->first)
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        @endif
                                <div class="group relative flex items-center gap-3 p-3.5 rounded-xl border border-main bg-secondary hover:border-primary/40 hover:shadow-sm transition-all">
                                    {{-- Icon --}}
                                    <div class="w-10 h-10 rounded-xl bg-primary-soft flex items-center justify-center flex-shrink-0 group-hover:bg-primary group-hover:text-white transition-all">
                                        <i class="{{$data->icon}} text-lg text-primary group-hover:text-white transition-colors"></i>
                                    </div>
                                    {{-- Info --}}
                                    <div class="min-w-0 flex-1">
                                        <p class="text-[10px] font-bold text-muted uppercase tracking-wider mb-0.5">{{__('Social')}} #{{$data->id}}</p>
                                        <p class="text-xs text-dark truncate" title="{{$data->url}}">{{$data->url}}</p>
                                    </div>
                                    {{-- Actions --}}
                                    <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0">
                                        <button type="button"
                                           class="w-7 h-7 rounded-lg bg-surface border border-main flex items-center justify-center text-primary hover:bg-primary hover:text-white hover:border-primary transition-all social_item_edit_btn"
                                           data-id="{{$data->id}}"
                                           data-url="{{$data->url}}"
                                           data-icon="{{$data->icon}}"
                                           title="{{__('Edit')}}">
                                            <i class="mdi mdi-pencil-outline text-xs"></i>
                                        </button>
                                        <x-delete-popover url="{{route('tenant.admin.delete.social.item', $data->id)}}"/>
                                    </div>
                                </div>
                        @if($loop->last)
                            </div>
                        @endif
                    @empty
                        <div class="flex flex-col items-center justify-center py-10 text-center">
                            <div class="w-16 h-16 rounded-2xl bg-secondary flex items-center justify-center mb-4">
                                <i class="mdi mdi-share-variant-outline text-3xl text-muted"></i>
                            </div>
                            <p class="text-sm font-semibold text-dark mb-1">{{__('No social icons yet')}}</p>
                            <p class="text-xs text-muted mb-4">{{__('Add your first social media link to get started')}}</p>
                            <button type="button" id="open_add_social_modal_empty"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-primary text-white text-xs font-bold hover:opacity-90 transition">
                                <i class="mdi mdi-plus text-sm"></i> {{__('Add Social Icon')}}
                            </button>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Topbar Menu --}}
            <div class="bg-surface rounded-xl shadow-main border border-main">
                <div class="px-4 sm:px-6 py-4 border-b border-main flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-[rgba(0,121,255,0.1)] flex items-center justify-center flex-shrink-0">
                        <i class="mdi mdi-menu text-[#0079FF] text-base"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Topbar Menu')}}</h3>
                        <p class="text-xs text-muted">{{__('Select which menu appears in topbar')}}</p>
                    </div>
                </div>

                <div class="p-4 sm:p-6 space-y-5">
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Select Menu')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-1 focus-within:border-primary transition">
                            <i class="mdi mdi-menu text-lg text-primary"></i>
                            <select name="topbar_menu" id="topbar_menu" class="flex-1 bg-transparent text-sm text-dark outline-none border-none focus:ring-0 p-0 py-2.5 appearance-none cursor-pointer">
                                @foreach($menu_list as $menu)
                                    <option value="{{$menu->id}}" {{$menu->id == $topbar_menu ? 'selected' : ''}}>{{$menu->title}}</option>
                                @endforeach
                            </select>
                            <i class="mdi mdi-chevron-down text-base text-primary pointer-events-none"></i>
                        </div>
                        <p class="mt-1.5 text-[11px] text-muted">{{__('The menu will be displayed in the top bar section')}} — <span class="text-primary font-medium">{{__('If available in the theme')}}</span></p>
                    </div>

                    {{-- Contact Info --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Phone')}}</label>
                            <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                                <i class="mdi mdi-phone-outline text-lg text-primary"></i>
                                <input type="text" name="topbar_phone" value="{{get_static_option('topbar_phone')}}" placeholder="{{__('Phone number')}}"
                                       class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Email')}}</label>
                            <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                                <i class="mdi mdi-email-outline text-lg text-primary"></i>
                                <input type="text" name="topbar_email" value="{{get_static_option('topbar_email')}}" placeholder="{{__('Email address')}}"
                                       class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Visibility Toggles --}}
            <div class="bg-surface rounded-xl shadow-main border border-main">
                <div class="px-4 sm:px-6 py-4 border-b border-main flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-[rgba(34,166,153,0.1)] flex items-center justify-center flex-shrink-0">
                        <i class="mdi mdi-toggle-switch-outline text-[#22A699] text-base"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Visibility Controls')}}</h3>
                        <p class="text-xs text-muted">{{__('Toggle topbar sections on or off')}}</p>
                    </div>
                </div>

                <div class="p-4 sm:p-6">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-3.5 rounded-xl bg-secondary border border-main hover:border-primary/30 transition">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-surface border border-main flex items-center justify-center flex-shrink-0">
                                    <i class="mdi mdi-menu text-sm text-primary"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-dark">{{__('Topbar Menu')}}</p>
                                    <p class="text-[11px] text-muted">{{__('Navigation menu in topbar')}}</p>
                                </div>
                            </div>
                            <x-fields.switcher name="topbar_menu_show_hide" value="{{get_static_option('topbar_menu_show_hide')}}"/>
                        </div>

                        <div class="flex items-center justify-between p-3.5 rounded-xl bg-secondary border border-main hover:border-primary/30 transition">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-surface border border-main flex items-center justify-center flex-shrink-0">
                                    <i class="mdi mdi-card-account-phone-outline text-sm text-primary"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-dark">{{__('Contact Info')}}</p>
                                    <p class="text-[11px] text-muted">{{__('Phone and email display')}}</p>
                                </div>
                            </div>
                            <x-fields.switcher name="contact_info_show_hide" value="{{get_static_option('contact_info_show_hide')}}"/>
                        </div>

                        <div class="flex items-center justify-between p-3.5 rounded-xl bg-secondary border border-main hover:border-primary/30 transition">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-surface border border-main flex items-center justify-center flex-shrink-0">
                                    <i class="mdi mdi-share-variant-outline text-sm text-primary"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-dark">{{__('Social Info')}}</p>
                                    <p class="text-[11px] text-muted">{{__('Social icon links')}}</p>
                                </div>
                            </div>
                            <x-fields.switcher name="social_info_show_hide" value="{{get_static_option('social_info_show_hide')}}"/>
                        </div>

                        <div class="flex items-center justify-between p-3.5 rounded-xl bg-secondary border border-main hover:border-primary/30 transition">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-surface border border-main flex items-center justify-center flex-shrink-0">
                                    <i class="mdi mdi-page-layout-header text-sm text-primary"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-dark">{{__('Full Topbar')}}</p>
                                    <p class="text-[11px] text-muted">{{__('Show or hide entire topbar')}}</p>
                                </div>
                            </div>
                            <x-fields.switcher name="topbar_show_hide" value="{{get_static_option('topbar_show_hide')}}"/>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Save Button --}}
            <button type="submit"
                    class="inline-flex items-center justify-center gap-2 px-8 py-3 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition shadow-lg shadow-primary/20">
                <i class="mdi mdi-content-save-outline text-base"></i> {{__('Save Changes')}}
            </button>
        </div>

        {{-- Right Column: Info Sidebar --}}
        <div class="xl:col-span-4">
            <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden xl:sticky xl:top-[80px]">
                <div class="px-4 sm:px-6 py-4 border-b border-main flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-info-soft flex items-center justify-center flex-shrink-0">
                        <i class="mdi mdi-lightbulb-outline text-info text-base"></i>
                    </div>
                    <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Quick Guide')}}</h3>
                </div>
                <div class="p-4 sm:p-6 space-y-4">
                    <div class="flex items-start gap-3">
                        <span class="inline-flex items-center justify-center w-5 h-5 rounded bg-primary text-white text-[10px] font-bold flex-shrink-0 mt-0.5">1</span>
                        <div>
                            <p class="text-xs font-semibold text-dark">{{__('Social Icons')}}</p>
                            <p class="text-[11px] text-muted mt-0.5">{{__('Add social media links that will appear in the topbar. Use the icon picker to select platform icons.')}}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="inline-flex items-center justify-center w-5 h-5 rounded bg-primary text-white text-[10px] font-bold flex-shrink-0 mt-0.5">2</span>
                        <div>
                            <p class="text-xs font-semibold text-dark">{{__('Topbar Menu')}}</p>
                            <p class="text-[11px] text-muted mt-0.5">{{__('Choose a navigation menu to display. Create menus from the Menu Builder section.')}}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="inline-flex items-center justify-center w-5 h-5 rounded bg-primary text-white text-[10px] font-bold flex-shrink-0 mt-0.5">3</span>
                        <div>
                            <p class="text-xs font-semibold text-dark">{{__('Visibility')}}</p>
                            <p class="text-[11px] text-muted mt-0.5">{{__('Use toggles to show/hide individual sections. Disabling "Full Topbar" hides everything.')}}</p>
                        </div>
                    </div>

                    <div class="mt-2 p-3 rounded-lg bg-[rgba(252,79,0,0.06)] border border-[rgba(252,79,0,0.15)]">
                        <div class="flex items-start gap-2">
                            <i class="mdi mdi-alert-circle-outline text-[#FC4F00] text-sm mt-0.5 flex-shrink-0"></i>
                            <p class="text-[11px] text-muted">{{__('Topbar features depend on the active theme. Some themes may not support all options.')}}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

{{-- Add Social Modal --}}
<div id="addSocialModal" class="hidden fixed inset-0 z-[800] flex items-center justify-center p-4">
    <div id="addSocialBackdrop" class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
    <div class="relative bg-surface rounded-2xl shadow-2xl border border-main w-full max-w-md overflow-hidden">
        <div class="px-6 py-4 border-b border-main flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-plus text-primary text-sm"></i>
                </div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Add Social Item')}}</h3>
            </div>
            <button type="button" class="modal_close w-8 h-8 rounded-lg bg-secondary border border-main flex items-center justify-center text-muted hover:text-danger hover:border-danger transition">
                <i class="mdi mdi-close text-base"></i>
            </button>
        </div>
        <form action="{{route('tenant.admin.new.social.item')}}" method="post">
            @csrf
            <div class="p-6 space-y-5">
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Icon')}}</label>
                    <div class="tw-iconpicker-wrap">
                        <button type="button" class="tw-icon-preview iconpicker-component" title="{{__('Current icon')}}">
                            <i class="ti tabler-user"></i>
                        </button>
                        <button type="button" class="icp icp-dd tw-icon-choose-btn">
                            <i class="mdi mdi-palette-outline text-sm"></i>
                            {{__('Choose Icon')}}
                        </button>
                    </div>
                    <input type="hidden" class="form-control" id="icon" value="ti tabler-user" name="icon">
                </div>
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('URL')}}</label>
                    <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                        <i class="mdi mdi-link-variant text-lg text-primary"></i>
                        <input type="text" name="url" id="social_item_link" placeholder="{{__('https://...')}}"
                               class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-main flex items-center justify-end gap-3">
                <button type="button" class="modal_close inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-secondary border border-main text-dark text-sm font-semibold hover:bg-muted transition">
                    {{__('Close')}}
                </button>
                <button type="submit" class="inline-flex items-center gap-1.5 px-6 py-2 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                    <i class="mdi mdi-plus-circle-outline text-base"></i> {{__('Add')}}
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Social Modal --}}
<div id="editSocialModal" class="hidden fixed inset-0 z-[800] flex items-center justify-center p-4">
    <div id="editSocialBackdrop" class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
    <div class="relative bg-surface rounded-2xl shadow-2xl border border-main w-full max-w-md overflow-hidden">
        <div class="px-6 py-4 border-b border-main flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-info-soft flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-pencil-outline text-info text-sm"></i>
                </div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Edit Social Item')}}</h3>
            </div>
            <button type="button" class="modal_close w-8 h-8 rounded-lg bg-secondary border border-main flex items-center justify-center text-muted hover:text-danger hover:border-danger transition">
                <i class="mdi mdi-close text-base"></i>
            </button>
        </div>
        <form action="{{route('tenant.admin.update.social.item')}}" method="post">
            @csrf
            <input type="hidden" name="id" id="social_item_id" value="">
            <div class="p-6 space-y-5">
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Icon')}}</label>
                    <div class="tw-iconpicker-wrap edit_icon">
                        <button type="button" class="tw-icon-preview iconpicker-component" title="{{__('Current icon')}}">
                            <i class="ti tabler-user"></i>
                        </button>
                        <button type="button" class="icp icp-dd tw-icon-choose-btn">
                            <i class="mdi mdi-palette-outline text-sm"></i>
                            {{__('Choose Icon')}}
                        </button>
                    </div>
                    <input type="hidden" class="form-control" id="edit_social_icon" value="ti tabler-user" name="icon">
                </div>
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('URL')}}</label>
                    <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                        <i class="mdi mdi-link-variant text-lg text-primary"></i>
                        <input type="text" name="url" id="social_item_edit_url" placeholder="{{__('https://...')}}"
                               class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-main flex items-center justify-end gap-3">
                <button type="button" class="modal_close inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-secondary border border-main text-dark text-sm font-semibold hover:bg-muted transition">
                    {{__('Close')}}
                </button>
                <button type="submit" class="inline-flex items-center gap-1.5 px-6 py-2 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                    <i class="mdi mdi-content-save-outline text-base"></i> {{__('Save Changes')}}
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
    <script>
        (function($){
            "use strict";

            function openModal(id) { document.getElementById(id).classList.remove('hidden'); document.body.classList.add('overflow-hidden'); }
            function closeModal(id) { document.getElementById(id).classList.add('hidden'); document.body.classList.remove('overflow-hidden'); }

            $(document).on('click', '#addSocialBackdrop', function () { closeModal('addSocialModal'); });
            $(document).on('click', '#editSocialBackdrop', function () { closeModal('editSocialModal'); });
            $(document).on('click', '.modal_close', function () {
                closeModal('addSocialModal');
                closeModal('editSocialModal');
            });

            $(document).on('click', '#open_add_social_modal, #open_add_social_modal_empty', function () {
                openModal('addSocialModal');
            });

            $(document).ready(function () {
                <x-icon-picker/>

                $(document).on('click', '.social_item_edit_btn', function () {
                    let el = $(this);
                    let id = el.data('id');
                    let url = el.data('url');
                    let icon = el.data('icon');

                    let form = $('#editSocialModal');
                    form.find('#social_item_id').val(id);
                    form.find('#edit_social_icon').val(icon);
                    form.find('#social_item_edit_url').val(url);
                    form.find('#edit_icon').val(el.data('icon'));
                    form.find('.edit_icon .icp-dd').attr('data-selected', el.data('icon'));
                    form.find('.edit_icon .iconpicker-component i').attr('class', el.data('icon'));

                    openModal('editSocialModal');
                });
            });
        })(jQuery);
    </script>
@endsection
