@extends('tenant.admin.admin-master')
@section('title') {{__('All Users')}} @endsection

@section('style')
    <x-datatable.tw-css/>
    <x-summernote.css/>
<style>.hover\:text-white:hover{color:#fff!important}</style>
@endsection

@section('content')

<x-landlord-error-msg/>
<x-landlord-flash-msg/>

{{-- Stats Strip --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 mb-5">
    @php
        $totalVerified   = $all_users->where('email_verified', 1)->count();
        $totalUnverified = $all_users->where('email_verified', 0)->count();
    @endphp
    <div class="stat-card bg-surface rounded-xl border border-main px-4 sm:px-5 py-4 flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
            <i class="mdi mdi-account-group text-primary text-base"></i>
        </div>
        <div>
            <p class="text-[10px] font-bold uppercase tracking-widest text-muted">{{__('Total Users')}}</p>
            <p class="text-lg font-bold text-dark leading-tight">{{$all_users->count()}}</p>
        </div>
    </div>
    <div class="stat-card bg-surface rounded-xl border border-main px-4 sm:px-5 py-4 flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-success-soft flex items-center justify-center flex-shrink-0">
            <i class="mdi mdi-email-check text-success text-base"></i>
        </div>
        <div>
            <p class="text-[10px] font-bold uppercase tracking-widest text-muted">{{__('Verified')}}</p>
            <p class="text-lg font-bold text-dark leading-tight">{{$totalVerified}}</p>
        </div>
    </div>
    <div class="stat-card bg-surface rounded-xl border border-main px-4 sm:px-5 py-4 flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-danger-soft flex items-center justify-center flex-shrink-0">
            <i class="mdi mdi-email-remove text-danger text-base"></i>
        </div>
        <div>
            <p class="text-[10px] font-bold uppercase tracking-widest text-muted">{{__('Unverified')}}</p>
            <p class="text-lg font-bold text-dark leading-tight">{{$totalUnverified}}</p>
        </div>
    </div>
    <div class="stat-card bg-surface rounded-xl border border-main px-4 sm:px-5 py-4 flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-warning-soft flex items-center justify-center flex-shrink-0">
            <i class="mdi mdi-delete-clock text-warning text-base"></i>
        </div>
        <div>
            <p class="text-[10px] font-bold uppercase tracking-widest text-muted">{{__('Trashed')}}</p>
            <p class="text-lg font-bold text-dark leading-tight">{{$trashed_users}}</p>
        </div>
    </div>
</div>

{{-- Table Card --}}
<div class="bg-surface rounded-xl shadow-main border border-main mb-6">

    {{-- Card Header --}}
    <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-account-multiple text-primary text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('All Users')}}</h3>
                <p class="text-xs text-muted">{{__('Manage all frontend user accounts')}}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{route('tenant.admin.user.new')}}"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition whitespace-nowrap">
                <i class="mdi mdi-plus text-base"></i>
                {{__('Add New')}}
            </a>
            <a href="{{route('tenant.admin.user.trash')}}"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-danger-soft border border-main text-danger text-sm font-semibold hover:bg-danger hover:text-white hover:border-danger transition whitespace-nowrap">
                <i class="mdi mdi-delete-clock text-base"></i>
                {{__('Trash')}} {{$trashed_users > 0 ? "({$trashed_users})" : ""}}
            </a>
        </div>
    </div>

    {{-- Table --}}
    <div class="tw-table-wrap">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-main">
                    <th class="hidden md:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest w-14">{{__('ID')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('User')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Username')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Verified')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest no-sort text-right">{{__('Actions')}}</th>
                </tr>
            </thead>
            <tbody>
            @foreach($all_users as $user)
                @php
                    $palettes = [
                        ['bg' => 'bg-primary-soft',  'text' => 'text-primary'],
                        ['bg' => 'bg-info-soft',     'text' => 'text-info'],
                        ['bg' => 'bg-[#f3e8ff]',     'text' => 'text-[#9333ea]'],
                        ['bg' => 'bg-warning-soft',  'text' => 'text-warning'],
                        ['bg' => 'bg-danger-soft',   'text' => 'text-danger'],
                    ];
                    $pal      = $palettes[$user->id % count($palettes)];
                    $initials = collect(explode(' ', $user->name))->map(fn($n) => strtoupper($n[0] ?? ''))->take(2)->join('');
                @endphp
                <tr class="border-b border-main hover:bg-muted transition-colors">
                    {{-- ID --}}
                    <td class="hidden md:table-cell px-4 py-3.5">
                        <span class="text-[11px] font-bold text-primary">{{__('#')}} {{$user->id}}</span>
                    </td>

                    {{-- User --}}
                    <td class="px-4 py-3.5">
                        <div class="flex items-center gap-3">
                            <span class="w-9 h-9 rounded-xl flex items-center justify-center text-[10px] font-bold flex-shrink-0 border-2 border-main {{ $pal['bg'] }} {{ $pal['text'] }} uppercase">
                                {{$initials}}
                            </span>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-dark truncate leading-tight">{{$user->name}}</p>
                                <p class="text-[11px] text-muted truncate mt-0.5 flex items-center gap-1">
                                    <i class="mdi mdi-at text-primary text-xs flex-shrink-0"></i>
                                    <span class="truncate max-w-[180px]">{{$user->email}}</span>
                                </p>
                            </div>
                        </div>
                    </td>

                    {{-- Username --}}
                    <td class="px-4 py-3.5">
                        <span class="text-sm font-medium text-dark">{{$user->username}}</span>
                    </td>

                    {{-- Verified --}}
                    <td class="px-4 py-3.5">
                        @if($user->email_verified === 0)
                            <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded bg-danger-soft text-danger text-[9px] font-bold uppercase">
                                <i class="mdi mdi-close-circle text-[9px]"></i>{{__('Unverified')}}
                            </span>
                        @else
                            <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded bg-success-soft text-success text-[9px] font-bold uppercase">
                                <i class="mdi mdi-check-circle text-[9px]"></i>{{__('Verified')}}
                            </span>
                        @endif
                    </td>

                    {{-- Actions --}}
                    <td class="px-4 py-3.5">
                        <div class="flex items-center justify-end">
                            <div class="row-action-wrap">

                                {{-- Edit --}}
                                <a href="{{route('tenant.admin.user.edit.profile', $user->id)}}"
                                   title="{{__('Edit')}}"
                                   class="w-9 h-9 mr-1 rounded-lg bg-primary-soft border border-main flex items-center justify-center text-primary hover:text-white hover:border-primary hover:bg-primary transition-all">
                                    <i class="mdi mdi-pencil-outline text-sm"></i>
                                </a>

                                {{-- Trigger button --}}
                                <button type="button" onclick="toggleRowMenu(this)"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-secondary border border-main text-dark text-xs font-semibold hover:bg-primary-soft hover:text-primary hover:border-primary transition-all">
                                    <i class="mdi mdi-cog-outline text-sm"></i>
                                    {{__('Actions')}}
                                    <i class="mdi mdi-chevron-down text-sm"></i>
                                </button>

                                {{-- Dropdown panel --}}
                                <div class="row-action-menu hidden">

                                    <div class="menu-divider"></div>

                                    {{-- Change Password --}}
                                    <button type="button" data-id="{{$user->id}}" class="action-item user_change_password_btn">
                                        <span class="action-icon bg-info-soft"><i class="mdi mdi-lock-reset text-info"></i></span>
                                        {{__('Change Password')}}
                                    </button>

                                    {{-- Send Mail --}}
                                    <button type="button" data-id="{{$user->email}}" class="action-item send_mail_to_tenant_btn">
                                        <span class="action-icon bg-warning-soft"><i class="mdi mdi-email-outline text-warning"></i></span>
                                        {{__('Send Mail')}}
                                    </button>

                                    {{-- Verify actions: only when email not verified --}}
                                    @if($user->email_verified < 1)
                                        <div class="menu-divider"></div>

                                        <form action="{{route(route_prefix().'admin.user.resend.verify.mail')}}" method="post">
                                            @csrf
                                            <input type="hidden" name="id" value="{{$user->id}}">
                                            <button type="submit" class="action-item">
                                                <span class="action-icon bg-[#f3e8ff]"><i class="mdi mdi-email-check-outline text-[#9333ea]"></i></span>
                                                {{__('Send Verify Mail')}}
                                            </button>
                                        </form>
                                    @endif

                                    <div class="menu-divider"></div>

                                    {{-- Delete --}}
                                    <button type="button" class="action-item action-danger swal_delete_button">
                                        <span class="action-icon bg-danger-soft"><i class="mdi mdi-delete-outline text-danger"></i></span>
                                        {{__('Delete User')}}
                                    </button>

                                </div>
                            </div>

                            {{-- Hidden delete form --}}
                            <form method="post" action="{{route('tenant.admin.user.delete', $user->id)}}" class="hidden d-none">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="swal_form_submit_btn hidden d-none"></button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Change Password Modal --}}
<div id="passwordModal" class="fixed inset-0 z-[800] hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal('passwordModal')"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4 pointer-events-none">
        <div class="bg-surface rounded-2xl shadow-xl border border-main w-full max-w-md pointer-events-auto">
            <div class="px-6 py-4 border-b border-main flex items-center justify-between">
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Change Password')}}</h3>
                <button type="button" onclick="closeModal('passwordModal')"
                        class="w-8 h-8 rounded-lg bg-secondary flex items-center justify-center text-muted hover:text-dark transition">
                    <i class="mdi mdi-close text-base"></i>
                </button>
            </div>
            <form action="{{route('tenant.admin.user.change.password')}}" id="user_password_change_modal_form" method="post">
                @csrf
                <div class="px-6 py-5 space-y-4">
                    <input type="hidden" name="ch_user_id" id="ch_user_id">
                    <div>
                        <label class="block text-xs font-semibold text-dark mb-1.5">{{__('Password')}}</label>
                        <div class="flex items-center gap-2 bg-secondary border border-main rounded-xl px-3 py-2.5 focus-within:border-primary transition">
                            <i class="mdi mdi-lock-outline text-muted text-base"></i>
                            <input type="password" name="password" placeholder="{{__('Enter Password')}}"
                                   class="flex-1 bg-transparent text-sm text-dark placeholder-gray-400 outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-dark mb-1.5">{{__('Confirm Password')}}</label>
                        <div class="flex items-center gap-2 bg-secondary border border-main rounded-xl px-3 py-2.5 focus-within:border-primary transition">
                            <i class="mdi mdi-lock-check-outline text-muted text-base"></i>
                            <input type="password" name="password_confirmation" placeholder="{{__('Confirm Password')}}"
                                   class="flex-1 bg-transparent text-sm text-dark placeholder-gray-400 outline-none">
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-main flex justify-end gap-2">
                    <button type="button" onclick="closeModal('passwordModal')"
                            class="px-4 py-2 rounded-xl bg-secondary border border-main text-sm font-semibold text-dark hover:bg-muted transition">
                        {{__('Cancel')}}
                    </button>
                    <button type="submit"
                            class="px-4 py-2 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                        {{__('Change Password')}}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Send Mail Modal --}}
<div id="sendMailModal" class="fixed inset-0 z-[800] hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal('sendMailModal')"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4 pointer-events-none">
        <div class="bg-surface rounded-2xl shadow-xl border border-main w-full max-w-lg pointer-events-auto">
            <div class="px-6 py-4 border-b border-main flex items-center justify-between">
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Send Mail')}}</h3>
                <button type="button" onclick="closeModal('sendMailModal')"
                        class="w-8 h-8 rounded-lg bg-secondary flex items-center justify-center text-muted hover:text-dark transition">
                    <i class="mdi mdi-close text-base"></i>
                </button>
            </div>
            <form action="{{route(route_prefix().'admin.user.send.mail')}}" id="send_mail_to_subscriber_edit_modal_form" method="post">
                @csrf
                <div class="px-6 py-5 space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-dark mb-1.5">{{__('Email')}}</label>
                        <div class="flex items-center gap-2 bg-secondary border border-main rounded-xl px-3 py-2.5">
                            <i class="mdi mdi-email-outline text-muted text-base"></i>
                            <input type="text" readonly id="email" name="email" placeholder="{{__('Email')}}"
                                   class="flex-1 bg-transparent text-sm text-dark placeholder-gray-400 outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-dark mb-1.5">{{__('Subject')}}</label>
                        <div class="flex items-center gap-2 bg-secondary border border-main rounded-xl px-3 py-2.5 focus-within:border-primary transition">
                            <i class="mdi mdi-format-title text-muted text-base"></i>
                            <input type="text" id="subject" name="subject" placeholder="{{__('Subject')}}"
                                   class="flex-1 bg-transparent text-sm text-dark placeholder-gray-400 outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-dark mb-1.5">{{__('Message')}}</label>
                        <input type="hidden" name="message">
                        <div class="summernote"></div>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-main flex justify-end gap-2">
                    <button type="button" onclick="closeModal('sendMailModal')"
                            class="px-4 py-2 rounded-xl bg-secondary border border-main text-sm font-semibold text-dark hover:bg-muted transition">
                        {{__('Cancel')}}
                    </button>
                    <button type="submit"
                            class="px-4 py-2 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                        {{__('Send Mail')}}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
    <x-datatable.tw-js/>
    <x-summernote.js/>

    <script>
    (function ($) {
        "use strict";

        /* ── Modal helpers ── */
        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }
        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
        window.openModal = openModal;
        window.closeModal = closeModal;

        /* ── Row-action dropdown (fixed-position, flips up near bottom) ── */
        window.toggleRowMenu = function (btn) {
            var menu = btn.nextElementSibling;
            var isHidden = menu.classList.contains('hidden');

            // close all open menus first
            document.querySelectorAll('.row-action-menu').forEach(function (m) {
                m.classList.add('hidden');
            });

            if (isHidden) {
                var rect       = btn.getBoundingClientRect();
                var menuHeight = 320;
                var spaceBelow = window.innerHeight - rect.bottom;
                var spaceAbove = rect.top;

                menu.style.right = (window.innerWidth - rect.right) + 'px';
                menu.style.left  = 'auto';

                if (spaceBelow >= Math.min(menuHeight, 200) || spaceBelow >= spaceAbove) {
                    menu.style.top    = (rect.bottom + 4) + 'px';
                    menu.style.bottom = 'auto';
                } else {
                    menu.style.bottom = (window.innerHeight - rect.top + 4) + 'px';
                    menu.style.top    = 'auto';
                }

                menu.classList.remove('hidden');
            }
        };

        // close on outside click
        document.addEventListener('click', function (e) {
            if (!e.target.closest('.row-action-wrap')) {
                document.querySelectorAll('.row-action-menu').forEach(function (m) {
                    m.classList.add('hidden');
                });
            }
        });

        // close on page scroll
        window.addEventListener('scroll', function (e) {
            if (e.target && e.target.closest && e.target.closest('.row-action-menu')) return;
            document.querySelectorAll('.row-action-menu').forEach(function (m) {
                m.classList.add('hidden');
            });
        }, true);

        // close on table horizontal scroll
        document.querySelectorAll('.tw-table-wrap').forEach(function (wrap) {
            wrap.addEventListener('scroll', function () {
                document.querySelectorAll('.row-action-menu').forEach(function (m) {
                    m.classList.add('hidden');
                });
            });
        });

        $(document).ready(function () {

            /* ── Change password btn ── */
            $(document).on('click', '.user_change_password_btn', function (e) {
                e.preventDefault();
                document.querySelectorAll('.row-action-menu').forEach(function (m) { m.classList.add('hidden'); });
                $('#ch_user_id').val($(this).data('id'));
                openModal('passwordModal');
            });

            /* ── Send mail btn ── */
            $(document).on('click', '.send_mail_to_tenant_btn', function () {
                document.querySelectorAll('.row-action-menu').forEach(function (m) { m.classList.add('hidden'); });
                $('#send_mail_to_subscriber_edit_modal_form').find('#email').val($(this).data('id'));
                openModal('sendMailModal');
            });

            /* ── Summernote ── */
            if ($.fn.summernote) {
                $('.summernote').summernote({
                    height: 200,
                    codemirror: { theme: 'monokai' },
                    callbacks: {
                        onChange: function (contents) {
                            $(this).prev('input').val(contents);
                        }
                    }
                });
            }
        });

    })(jQuery);
    </script>
@endsection
