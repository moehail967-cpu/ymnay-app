@extends(route_prefix().'admin.admin-master')
@section('title') {{__('Add New Role')}} @endsection

@section('content')

    <div class="bg-surface rounded-xl shadow-main overflow-hidden">

        <div class="px-6 py-4 border-b border-main flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-success-soft flex items-center justify-center">
                    <i class="mdi mdi-shield-plus text-success text-base"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-dark font-urbanist">{{__('New Role')}}</h3>
                    <p class="text-xs text-muted">{{__('Define a role and assign permissions')}}</p>
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

            <form action="{{route(route_prefix().'admin.role.new')}}" method="post" enctype="multipart/form-data">
                @csrf

                <div class="mb-5">
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Role Name')}}</label>
                    <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-1 focus-within:border-primary transition">
                        <i class="mdi mdi-shield-outline text-lg text-primary"></i>
                        <input type="text" name="name" placeholder="{{__('Enter role name')}}" value="{{old('name')}}"
                               class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                    </div>
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
                                        <input type="checkbox" id="perm_{{$perm->id}}" name="permission[]" value="{{$perm->id}}" class="permission-checkbox">
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
                        <i class="mdi mdi-shield-check text-base"></i> {{__('Create Role')}}
                    </button>
                </div>
            </form>
        </div>
    </div>

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
