@extends(route_prefix().'admin.admin-master')
@section('title') {{__('All Admin Roles')}} @endsection

@section('style')
    <x-datatable.tw-css/>
<style>.hover\:text-white:hover{color:#fff!important}</style>
@endsection

@section('content')

    <x-landlord-error-msg/>
    <x-landlord-flash-msg/>

    <div class="bg-surface rounded-xl shadow-main overflow-hidden mb-6">

        {{-- Card Header --}}
        <div class="px-6 py-4 border-b border-main flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-success-soft flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-shield-key text-success text-base"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-dark font-urbanist">{{__('All Admin Roles')}}</h3>
                    <p class="text-xs text-muted">{{__('Manage roles and their permissions')}}</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{route(route_prefix().'admin.role.new')}}"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-primary text-white text-sm font-semibold hover:opacity-90 transition whitespace-nowrap">
                    <i class="mdi mdi-plus text-base"></i> {{__('New Role')}}
                </a>
            </div>
        </div>

        {{-- Table --}}
        <div class="tw-table-wrap">
            <table class="w-full text-left border-collapse" id="rolesTable">
                <thead>
                    <tr class="border-b border-main">
                        <th class="px-5 py-3 text-[11px] font-bold text-subtle uppercase tracking-wider w-16">{{__('ID')}}</th>
                        <th class="px-5 py-3 text-[11px] font-bold text-subtle uppercase tracking-wider">{{__('Role Name')}}</th>
                        <th class="px-5 py-3 text-[11px] font-bold text-subtle uppercase tracking-wider no-sort text-right">{{__('Actions')}}</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($roles as $role)
                    <tr class="border-b border-main/50 hover:bg-muted/30 transition-colors">

                        {{-- ID --}}
                        <td class="px-5 py-3.5">
                            <span class="text-sm font-semibold text-subtle">#{{$role->id}}</span>
                        </td>

                        {{-- Name --}}
                        <td class="px-5 py-3.5">
                            @if($role->name === 'Super Admin')
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-bold text-dark">{{$role->name}}</span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide bg-danger-soft text-danger border border-danger/20">
                                        {{__('Full Access')}}
                                    </span>
                                </div>
                            @else
                                <span class="text-sm font-semibold text-dark">{{$role->name}}</span>
                            @endif
                        </td>

                        {{-- Actions --}}
                        <td class="px-5 py-3.5">
                            <div class="flex items-center justify-end gap-1.5">
                                @if($role->name !== 'Super Admin')
                                    <a href="{{route(route_prefix().'admin.user.role.edit', $role->id)}}"
                                       title="{{__('Edit')}}"
                                       class="w-8 h-8 rounded-lg bg-primary-soft border border-main flex items-center justify-center text-primary hover:bg-primary hover:text-white hover:border-primary transition-all">
                                        <i class="mdi mdi-pencil-outline text-sm"></i>
                                    </a>
                                    <button type="button"
                                            title="{{__('Delete')}}"
                                            class="swal_delete_button w-8 h-8 rounded-lg bg-danger-soft border border-main flex items-center justify-center text-danger hover:bg-danger hover:text-white hover:border-danger transition-all">
                                        <i class="mdi mdi-delete-outline text-sm"></i>
                                    </button>
                                    <form method="post"
                                          action="{{route(route_prefix().'admin.user.role.delete', $role->id)}}"
                                          class="hidden d-none">
                                        @csrf
                                        <button type="submit" class="swal_form_submit_btn hidden d-none"></button>
                                    </form>
                                @else
                                    <span class="text-xs text-subtle italic">—</span>
                                @endif
                            </div>
                        </td>

                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection

@section('scripts')
    <x-datatable.tw-js/>
    <script>
    (function ($) {
        "use strict";
        $(document).ready(function () {
            var table = $('#rolesTable').DataTable();
            $('#roleTableSearch').on('input', function () {
                table.search(this.value).draw();
            });
        });
    })(jQuery);
    </script>
@endsection
