@extends(route_prefix().'admin.admin-master')
@section('title') {{__('All Admins')}} @endsection

@section('style')
    <x-datatable.tw-css/>
<style>.hover\:text-white:hover{color:#fff!important}</style>
@endsection

@section('content')

    <x-landlord-flash-msg/>
    <x-landlord-error-msg/>

    {{-- Stats Strip --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 mb-5">
        @php
            $totalRoles = $all_user->flatMap(fn($u) => $u->getRoleNames())->unique()->count();
        @endphp
        <div class="stat-card bg-surface rounded-xl border border-main px-4 sm:px-5 py-4 flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-account-group text-primary text-base"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-widest text-muted">{{__('Total Admins')}}</p>
                <p class="text-lg font-bold text-dark leading-tight">{{$all_user->count()}}</p>
            </div>
        </div>
        <div class="stat-card bg-surface rounded-xl border border-main px-4 sm:px-5 py-4 flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-shield-star text-primary text-base"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-widest text-muted">{{__('Unique Roles')}}</p>
                <p class="text-lg font-bold text-dark leading-tight">{{$totalRoles}}</p>
            </div>
        </div>
        <div class="stat-card bg-surface rounded-xl border border-main px-4 sm:px-5 py-4 flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-success-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-account-check text-success text-base"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-widest text-muted">{{__('With Avatar')}}</p>
                <p class="text-lg font-bold text-dark leading-tight">{{$all_user->filter(fn($u) => !empty($u->image))->count()}}</p>
            </div>
        </div>
        <div class="stat-card bg-surface rounded-xl border border-main px-4 sm:px-5 py-4 flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-warning-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-account-badge text-warning text-base"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-widest text-muted">{{__('No Role')}}</p>
                <p class="text-lg font-bold text-dark leading-tight">{{$all_user->filter(fn($u) => $u->getRoleNames()->isEmpty())->count()}}</p>
            </div>
        </div>
    </div>

    <div class="bg-surface rounded-xl shadow-main overflow-hidden mb-6">

        {{-- Card Header --}}
        <div class="px-4 sm:px-6 py-4 border-b border-main flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-shield-account text-primary text-base"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-dark font-urbanist">{{__('All Admins')}}</h3>
                    <p class="text-xs text-muted">{{__('All admin accounts created by super admin')}}</p>
                </div>
            </div>
            <div class="flex items-center gap-2.5 w-full sm:w-auto">
                <a href="{{route(route_prefix().'admin.new.user')}}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition whitespace-nowrap flex-shrink-0">
                    <i class="mdi mdi-plus text-base"></i>
                    <span class="hidden xs:inline">{{__('New Admin')}}</span>
                    <span class="xs:hidden">{{__('New')}}</span>
                </a>
            </div>
        </div>

        {{-- Table --}}
        <div class="tw-table-wrap">
            <table class="w-full text-left border-collapse" id="allAdminTable">
                <thead>
                    <tr class="border-b border-main">
                        <th class="hidden md:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest w-14">{{__('ID')}}</th>
                        <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Admin')}}</th>
                        <th class="hidden sm:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Contact')}}</th>
                        <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Role')}}</th>
                        <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest no-sort text-right">{{__('Actions')}}</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($all_user as $data)
                    @php
                        $img      = get_attachment_image_by_id($data->image, null, true);
                        $palettes = [
                            ['bg' => 'bg-primary-soft',  'text' => 'text-primary'],
                            ['bg' => 'bg-info-soft',     'text' => 'text-info'],
                            ['bg' => 'bg-[#f3e8ff]',     'text' => 'text-[#9333ea]'],
                            ['bg' => 'bg-warning-soft',  'text' => 'text-warning'],
                            ['bg' => 'bg-danger-soft',   'text' => 'text-danger'],
                        ];
                        $pal      = $palettes[$data->id % count($palettes)];
                        $initials = collect(explode(' ', $data->name))->map(fn($n) => strtoupper($n[0] ?? ''))->take(2)->join('');
                    @endphp
                    <tr class="border-b border-main hover:bg-muted transition-colors">

                        {{-- ID --}}
                        <td class="hidden md:table-cell px-4 py-3.5">
                            <span class="inline-flex items-center justify-center text-[11px] font-bold text-primary">
                               {{__('#')}} {{$data->id}}
                            </span>
                        </td>

                        {{-- Admin: avatar + name + username --}}
                        <td class="px-4 py-3.5">
                            <div class="flex items-center gap-3">
                                @if(!empty($img))
                                    <img src="{{$img['img_url']}}" alt="{{$data->name}}"
                                         class="w-9 h-9 rounded-xl object-cover border-2 border-main flex-shrink-0">
                                @else
                                    <span class="w-9 h-9 rounded-xl flex items-center justify-center text-[10px] font-bold flex-shrink-0 border-2 border-main {{ $pal['bg'] }} {{ $pal['text'] }} uppercase">
                                        {{$initials}}
                                    </span>
                                @endif
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-dark truncate leading-tight">{{$data->name}}</p>
                                    <p class="text-[11px] text-muted truncate mt-0.5 flex items-center gap-1">
                                        <i class="mdi mdi-identifier text-primary text-xs"></i>
                                        {{$data->username}}
                                    </p>
                                    @if(!empty($data->email))
                                    <p class="sm:hidden text-[11px] text-muted truncate mt-0.5 flex items-center gap-1">
                                        <i class="mdi mdi-at text-primary text-xs"></i>
                                        {{$data->email}}
                                    </p>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- Contact --}}
                        <td class="hidden sm:table-cell px-4 py-3.5">
                            <p class="text-xs text-dark flex items-center gap-1.5 font-medium">
                                <i class="mdi mdi-at text-primary text-sm flex-shrink-0"></i>
                                <span class="truncate max-w-[160px]">{{$data->email ?? '—'}}</span>
                            </p>
                            @if(!empty($data->mobile))
                            <p class="text-[11px] text-muted flex items-center gap-1.5 mt-1">
                                <i class="mdi mdi-phone-outline text-primary text-sm flex-shrink-0"></i>
                                <span>{{$data->mobile}}</span>
                            </p>
                            @endif
                        </td>

                        {{-- Role badges --}}
                        <td class="px-4 py-3.5">
                            <div class="flex flex-wrap gap-1.5">
                                @forelse($data->getRoleNames() as $v)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-primary-soft text-primary text-[10px] font-bold uppercase tracking-tight border border-main">
                                        <i class="mdi mdi-shield-check text-xs"></i>
                                        {{$v}}
                                    </span>
                                @empty
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-muted text-subtle text-[10px] font-bold uppercase tracking-tight border border-main">
                                        <i class="mdi mdi-minus-circle-outline text-xs"></i>
                                        {{__('No role')}}
                                    </span>
                                @endforelse
                            </div>
                        </td>

                        {{-- Inline Actions --}}
                        <td class="px-4 py-3.5">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{route(route_prefix().'admin.user.edit', $data->id)}}"
                                   title="{{__('Edit')}}"
                                   class="w-8 h-8 rounded-lg bg-primary-soft border border-main flex items-center justify-center text-primary hover:text-white hover:bg-primary transition-all">
                                    <i class="mdi mdi-pencil-outline text-sm"></i>
                                </a>
                                <button type="button"
                                        data-id="{{$data->id}}"
                                        title="{{__('Change Password')}}"
                                        class="user_change_password_btn w-8 h-8 rounded-lg bg-info-soft border border-main flex items-center justify-center text-info hover:bg-info hover:text-white hover:border-info transition-all">
                                    <i class="mdi mdi-lock-reset text-sm"></i>
                                </button>
                                <button type="button"
                                        title="{{__('Delete')}}"
                                        class="swal_delete_button w-8 h-8 rounded-lg bg-danger-soft border border-main flex items-center justify-center text-danger hover:bg-danger hover:text-white hover:border-danger transition-all">
                                    <i class="mdi mdi-delete-outline text-sm"></i>
                                </button>
                                <form method="post"
                                      action="{{route(route_prefix().'admin.delete.user', $data->id)}}"
                                      class="hidden d-none">
                                    @csrf
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
    <div id="user_change_password_modal" class="hidden fixed inset-0 z-[999] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" id="pwd_modal_backdrop"></div>
        <div class="relative bg-surface rounded-2xl shadow-main w-full max-w-md overflow-hidden border border-main">

            {{-- Modal Header --}}
            <div class="flex items-center gap-3 px-6 py-4 border-b border-main bg-secondary">
                <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-lock-reset text-primary text-base"></i>
                </div>
                <div class="flex-1">
                    <h5 class="text-sm font-bold text-dark font-urbanist">{{__('Change Admin Password')}}</h5>
                    <p class="text-[11px] text-muted">{{__('Set a new secure password for this admin')}}</p>
                </div>
                <button type="button" id="pwd_modal_close"
                        class="w-8 h-8 rounded-full flex items-center justify-center text-muted hover:bg-primary-soft hover:text-primary transition">
                    <i class="mdi mdi-close text-lg"></i>
                </button>
            </div>

            <x-landlord-error-msg/>

            <form action="{{route(route_prefix().'admin.user.password.change')}}"
                  id="user_password_change_modal_form" method="post">
                @csrf
                <div class="px-6 py-5 space-y-4">
                    <input type="hidden" name="ch_user_id" id="ch_user_id">

                    {{-- New Password --}}
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('New Password')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                            <i class="mdi mdi-lock-outline text-lg text-primary"></i>
                            <input type="password" name="password"
                                   placeholder="{{__('Enter new password')}}"
                                   class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                        </div>
                    </div>

                    {{-- Confirm Password --}}
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Confirm Password')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                            <i class="mdi mdi-lock-check-outline text-lg text-primary"></i>
                            <input type="password" name="password_confirmation"
                                   placeholder="{{__('Re-enter password')}}"
                                   class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                        </div>
                    </div>

                    <p class="text-[11px] text-muted flex items-center gap-1.5 pt-1">
                        <i class="mdi mdi-information-outline text-primary text-sm"></i>
                        {{__('Use at least 8 characters including numbers and symbols.')}}
                    </p>
                </div>

                <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-main bg-secondary">
                    <button type="button" id="pwd_modal_cancel"
                            class="px-4 py-2 text-sm font-medium text-dark bg-surface border border-main rounded-xl hover:bg-muted transition">
                        {{__('Cancel')}}
                    </button>
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2 text-sm font-semibold text-white bg-primary rounded-xl hover:opacity-90 transition">
                        <i class="mdi mdi-check text-base"></i>
                        {{__('Update Password')}}
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection

@section('scripts')
    <x-datatable.tw-js/>
    <script>
    (function ($) {
        "use strict";
        $(document).ready(function () {
            var table = $('#allAdminTable').DataTable();
            $('#adminTableSearch').on('input', function () {
                table.search(this.value).draw();
            });

            // Password modal open
            $(document).on('click', '.user_change_password_btn', function (e) {
                e.preventDefault();
                document.querySelectorAll('.row-action-menu').forEach(function(m){ m.classList.add('hidden'); });
                $('#ch_user_id').val($(this).data('id'));
                $('#user_change_password_modal').removeClass('hidden');
                $('body').addClass('overflow-hidden');
            });

            function closePwdModal() {
                $('#user_change_password_modal').addClass('hidden');
                $('body').removeClass('overflow-hidden');
            }
            $('#pwd_modal_close, #pwd_modal_cancel, #pwd_modal_backdrop').on('click', closePwdModal);
        });
    })(jQuery);
    </script>
@endsection
