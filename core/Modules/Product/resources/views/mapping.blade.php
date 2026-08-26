@extends('tenant.admin.admin-master')
@section('title')
    {{__('Import Product')}}
@endsection
@section('site-title')
    {{__('Import Product')}}
@endsection

@section('content')

{{-- Field Mapping Card --}}
<div class="bg-surface rounded-xl shadow-main border border-main">
    <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
            <i class="mdi mdi-swap-horizontal text-primary text-base"></i>
        </div>
        <div>
            <h3 class="text-sm font-bold text-dark font-urbanist">{{__('CSV Field Mapping')}}</h3>
            <p class="text-xs text-muted">{{__('Map your CSV columns to database fields.')}} <span class="text-danger">*{{__('Required fields')}}</span></p>
        </div>
    </div>

    <div class="p-4 sm:p-6">
        <form action="{{ route('tenant.products.import.post') }}" method="POST">
            @csrf
            <input type="hidden" name="csv_file" value="{{ $filePath }}">

            <div class="tw-table-wrap">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-main">
                            <th class="px-4 py-3 text-[10px] font-bold tracking-widest text-muted uppercase w-[40%]">{{__('Database Field')}}</th>
                            <th class="px-4 py-3 text-[10px] font-bold tracking-widest text-muted uppercase w-[60%]">{{__('CSV Column')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dbFields as $dbField => $label)
                            <tr class="border-b border-main last:border-b-0 hover:bg-secondary/50 transition-colors">
                                <td class="px-4 py-3">
                                    <span class="text-sm font-semibold text-dark">{{ $label }}</span>
                                    @if($dbField == 'name')
                                        <span class="text-danger">*</span>
                                    @endif
                                    <br>
                                    <span class="text-xs text-muted">{{ $dbField }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <select name="mapping[{{ $dbField }}]" class="lnd-input">
                                        <option value="">-- {{__('Skip this field')}} --</option>
                                        @foreach($header as $col)
                                            <option value="{{ $col }}"
                                                {{ strtolower(str_replace(' ', '_', $col)) == $dbField ? 'selected' : '' }}>
                                                {{ $col }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex flex-wrap items-center gap-3 mt-6 pt-5 border-t border-main">
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-success text-white text-sm font-semibold hover:opacity-90 transition">
                    <i class="mdi mdi-upload"></i>
                    {{__('Import Products Now')}}
                </button>
                <a href="{{ route('tenant.products.import') }}" class="inline-flex items-center gap-2 px-5 py-2 rounded-xl text-sm font-semibold text-dark bg-secondary border border-main hover:border-hover transition">
                    <i class="mdi mdi-close"></i>
                    {{__('Cancel')}}
                </a>
            </div>
        </form>
    </div>
</div>

@endsection
