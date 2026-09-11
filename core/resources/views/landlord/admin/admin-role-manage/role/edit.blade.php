@extends(route_prefix().'admin.admin-master')
@section('title') {{__('Edit Role')}} @endsection

@section('content')

@if(is_null(tenant()))

    {{-- ===== LANDLORD · TAILWIND UI ===== --}}
    <div class="bg-surface rounded-xl shadow-main overflow-hidden">

        <div class="px-6 py-4 border-b border-main flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-success-soft flex items-center justify-center">
                    <i class="mdi mdi-shield-edit text-success text-base"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Edit Role')}}</h3>
                    <p class="text-xs text-muted">{{__('Update role name and permissions')}}</p>
                </div>
            </div>
            <a href="{{route(route_prefix().'admin.all.admin.role')}}"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg border border-main text-sm font-medium text-brand hover:border-primary hover:text-primary transition">
                <i class="mdi mdi-arrow-left text-base"></i> {{__('All Roles')}}
            </a>
        </div>

        <div class="p-6">
            <x-landlord-error-msg/>
            <x-landlord-flash-msg/>

            <form action="{{route(route_prefix().'admin.user.role.update')}}" method="post" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" value="{{$role->id}}">

                <div class="mb-5">
                    <label class="lnd-label">{{__('Role Name')}}</label>
                    <input type="text" class="lnd-input" name="name" value="{{$role->name}}" placeholder="{{__('Enter role name')}}">
                </div>

                {{-- Check All button --}}
                <div class="flex items-center justify-between mb-4">
                    <p class="text-sm font-semibold text-dark">{{__('Permissions')}}</p>
                    <button type="button"
                            class="checked_all inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg border border-main text-xs font-semibold text-brand hover:border-primary hover:text-primary transition">
                        <i class="mdi mdi-checkbox-multiple-marked-outline text-sm"></i>
                        {{__('Check All')}}
                    </button>
                </div>

                {{-- Permission groups --}}
                <div class="space-y-6 checkbox-wrapper">
                    @foreach($permissions ?? [] as $index => $permission)
                        <div>
                            <p class="perm-group-title">{{$index}}</p>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-2">
                                @foreach($permission ?? [] as $perm)
                                    <label class="perm-item" for="perm_{{$perm->id}}">
                                        <input type="checkbox" id="perm_{{$perm->id}}" name="permission[]"
                                               value="{{$perm->id}}"
                                               @if(in_array($perm->id, $rolePermissions)) checked @endif
                                               class="permission-checkbox">
                                        <span>{{__(ucfirst(str_replace('-', ' ', $perm->name)))}}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 pt-5 border-t border-main">
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                        <i class="mdi mdi-content-save text-base"></i> {{__('Update Role')}}
                    </button>
                </div>
            </form>
        </div>
    </div>

@else

    {{-- ===== TENANT · BOOTSTRAP UI (unchanged) ===== --}}
    <div class="col-lg-12 col-ml-12">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="header-wrap d-flex justify-content-between">
                            <h4 class="header-title mb-4">{{__('Edit Role')}}</h4>
                            <div class="btn-wrapper">
                                <a href="{{route(route_prefix().'admin.all.admin.role')}}" class="btn btn-info">{{__('All Roles')}}</a>
                            </div>
                        </div>
                        <x-error-msg/>
                        <x-flash-msg/>
                        <form action="{{route(route_prefix().'admin.user.role.update')}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="id" value="{{$role->id}}">
                            <div class="form-group">
                                <label for="name">{{__('Name')}}</label>
                                <input type="text" class="form-control" value="{{$role->name}}" name="name" placeholder="{{__('Enter name')}}">
                            </div>
                            <button type="button" class="btn btn-xs mb-4 btn-outline-dark checked_all">{{__('Check All')}}</button>
                            <div class="row checkbox-wrapper">
                                @foreach($permissions ?? [] as $index => $permission)
                                    <div class="permission-prefix mt-4">
                                        <h4 class="text-capitalize">{{$index}}</h4>
                                        <hr>
                                    </div>
                                    @foreach($permission ?? [] as $perm)
                                        <div class="col-lg-2 col-md-2">
                                            <div class="form-group">
                                                <label><strong>{{__(ucfirst(str_replace('-',' ',$perm->name)))}}</strong></label>
                                                <label class="switch role">
                                                    <input type="checkbox" name="permission[]" @if(in_array($perm->id, $rolePermissions)) checked @endif value="{{$perm->id}}">
                                                    <span class="slider onff"></span>
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                @endforeach
                            </div>
                            <button type="submit" class="btn btn-primary mt-4 pr-4 pl-4">{{__('Update')}}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endif

@endsection

@section('scripts')
    <script>
    (function ($) {
        "use strict";
        $(document).ready(function () {
            var currentState = false;
            $(document).on('click', '.checked_all', function () {
                var allCheckbox = $('.checkbox-wrapper input[type="checkbox"]');
                $.each(allCheckbox, function () {
                    $(this).prop('checked', !currentState);
                });
                currentState = !currentState;
                currentState
                    ? $(this).text('{{__('Uncheck All')}}')
                    : $(this).text('{{__('Check All')}}');
            });
        });
    })(jQuery);
    </script>
@endsection
