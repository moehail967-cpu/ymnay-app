@extends('tenant.admin.admin-master')
@section('title') {{__('Mobile Campaign')}} @endsection

@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

<form action="{{ route('tenant.admin.mobile.campaign.update') }}" method="post">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        {{-- Main Form --}}
        <div class="lg:col-span-9">
            <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">

                <div class="px-4 sm:px-6 py-4 border-b border-main flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                        <i class="mdi mdi-bullhorn-outline text-primary text-base"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Mobile Campaign')}}</h3>
                        <p class="text-xs text-muted">{{__('Select the campaign to display in the mobile app')}}</p>
                    </div>
                </div>

                <div class="p-4 sm:p-6 space-y-5">
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Select Campaign')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                            <i class="mdi mdi-tag-outline text-lg text-primary"></i>
                            <select name="campaign" class="flex-1 bg-transparent text-sm text-dark outline-none border-none focus:ring-0 p-0 cursor-pointer">
                                <option value="">{{__('Select Campaign')}}</option>
                                @foreach($campaigns as $item)
                                    <option value="{{ $item->id }}" {{ $item->id == optional($selectedCampaign)->campaign_id ? 'selected' : '' }}>{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="lg:col-span-3">
            <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden sticky top-4">
                <div class="px-4 py-4 border-b border-main">
                    <h4 class="text-xs font-bold text-dark uppercase tracking-widest">{{__('Actions')}}</h4>
                </div>
                <div class="p-4 space-y-5">
                    <div class="flex items-start gap-2.5 bg-blue-50 border border-blue-100 rounded-xl px-4 py-3">
                        <i class="mdi mdi-information-outline text-info text-lg mt-0.5 shrink-0"></i>
                        <span class="text-[11px] text-dark leading-relaxed">{{__('The selected campaign will be featured in the mobile application.')}}</span>
                    </div>
                    <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                        <i class="mdi mdi-content-save-outline text-base"></i> {{__('Update Campaign')}}
                    </button>
                </div>
            </div>
        </div>

    </div>
</form>

@endsection
