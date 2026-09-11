@extends('tenant.admin.admin-master')
@section('title')
    {{__('Sales Report Settings')}}
@endsection

@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    <div class="lg:col-span-9">
        <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">
            <div class="px-4 sm:px-6 py-4 border-b border-main flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-cog-outline text-primary text-base"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Sales Report Settings')}}</h3>
                    <p class="text-xs text-muted">{{__('Configure your weekly report preferences')}}</p>
                </div>
            </div>

            <div class="p-4 sm:p-6">
                <form action="{{route('tenant.admin.sales.settings')}}" method="POST">
                    @csrf
                    <div class="space-y-5">
                        <div>
                            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Week Starting Day')}}</label>
                            <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-1 focus-within:border-primary transition">
                                <i class="mdi mdi-calendar-week text-lg text-primary"></i>
                                <select name="first_workday" class="flex-1 bg-transparent text-sm text-dark outline-none border-none focus:ring-0 p-0 appearance-none cursor-pointer">
                                    @foreach($daysOfWeek as $key => $day)
                                        <option value="{{$key}}" {{get_static_option('first_workday') == $key ? 'selected' : ''}}>{{__($day)}}</option>
                                    @endforeach
                                </select>
                                <i class="mdi mdi-chevron-down text-base text-primary pointer-events-none"></i>
                            </div>
                        </div>

                        <button type="submit"
                                class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                            <i class="mdi mdi-content-save-outline text-base"></i> {{__('Update Settings')}}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
