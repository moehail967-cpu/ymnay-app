@extends('tenant.admin.admin-master')
@section('title') {{__('Import Countries')}} @endsection

@section('content')

    <x-landlord-error-msg/>
    <x-landlord-flash-msg/>

    <div class="max-w-3xl">
        <div class="bg-surface rounded-xl shadow-main overflow-hidden mb-6">

            {{-- Card Header --}}
            <div class="px-5 sm:px-6 py-4 border-b border-main flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                        <i class="mdi mdi-database-import text-primary text-base"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Import Countries')}}</h3>
                        <p class="text-xs text-muted">{{__('Upload a CSV file to bulk-import countries')}}</p>
                    </div>
                </div>
                <a href="{{route('tenant.admin.settings.csv.download.sample', 'country')}}"
                   download="sample-country-data.csv"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg border border-main text-sm font-medium text-brand hover:border-primary hover:text-primary transition">
                    <i class="mdi mdi-file-download-outline text-base"></i> {{__('Sample CSV')}}
                </a>
            </div>

            <div class="p-5 sm:p-6">
                @if(empty($import_data))
                    {{-- Step 1: Upload CSV --}}
                    <form action="{{route('tenant.admin.country.import.csv.update.settings')}}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-5">
                            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('CSV File')}}</label>
                            <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                                <i class="mdi mdi-file-delimited text-lg text-primary"></i>
                                <input type="file" name="csv_file" accept=".csv" required
                                       class="flex-1 bg-transparent text-sm text-dark outline-none file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary-soft file:text-primary hover:file:bg-primary hover:file:text-white file:transition file:cursor-pointer">
                            </div>
                            <p class="text-[11px] text-muted mt-1.5 flex items-center gap-1">
                                <i class="mdi mdi-information-outline text-primary text-sm"></i>
                                {{__('Only CSV files separated by comma (,) are allowed.')}}
                            </p>
                        </div>
                        <button type="submit"
                                class="loading-btn inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                            <i class="mdi mdi-upload text-base"></i> {{__('Upload & Continue')}}
                        </button>
                    </form>
                @else
                    {{-- Step 2: Map Fields --}}
                    @php
                        $option_markup = '';
                        foreach(current($import_data) as $map_item){
                            $option_markup .= '<option value="'.trim($map_item).'">'.$map_item.'</option>';
                        }
                    @endphp

                    <form action="{{route('tenant.admin.country.import.database')}}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="space-y-4 mb-6">
                            {{-- Title field --}}
                            <div class="bg-secondary rounded-xl border border-main p-4">
                                <div class="flex items-center justify-between gap-4 flex-wrap">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                                            <i class="mdi mdi-flag text-primary text-sm"></i>
                                        </div>
                                        <p class="text-sm font-semibold text-dark">{{__('Country Name')}}</p>
                                    </div>
                                    <div class="w-full sm:w-48">
                                        <select class="mapping_select w-full bg-surface border border-main rounded-lg px-3 py-2 text-sm text-dark outline-none focus:border-primary transition">
                                            <option value="">{{__('Select Field')}}</option>
                                            {!! $option_markup !!}
                                        </select>
                                        <input type="hidden" name="country">
                                    </div>
                                </div>
                                <p class="text-[11px] text-muted mt-2 ml-10">{{__('Only unique countries will be added automatically.')}}</p>
                            </div>

                            {{-- Status field --}}
                            <div class="bg-secondary rounded-xl border border-main p-4">
                                <div class="flex items-center justify-between gap-4 flex-wrap">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-lg bg-success-soft flex items-center justify-center flex-shrink-0">
                                            <i class="mdi mdi-toggle-switch text-success text-sm"></i>
                                        </div>
                                        <p class="text-sm font-semibold text-dark">{{__('Status')}}</p>
                                    </div>
                                    <div class="w-full sm:w-48">
                                        <select class="mapping_select w-full bg-surface border border-main rounded-lg px-3 py-2 text-sm text-dark outline-none focus:border-primary transition">
                                            <option value="publish">{{__('Publish')}}</option>
                                            <option value="draft">{{__('Draft')}}</option>
                                        </select>
                                        <input type="hidden" name="status" value="publish">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="submit"
                                class="loading-btn inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-success text-white text-sm font-semibold hover:opacity-90 transition">
                            <i class="mdi mdi-database-import text-base"></i> {{__('Import')}}
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
    (function ($) {
        "use strict";
        $(document).ready(function () {
            $(document).on('click', '.loading-btn', function () {
                $(this).append(' <i class="mdi mdi-loading mdi-spin text-base"></i>');
            });
            $(document).on('change', '.mapping_select', function () {
                $('.mapping_select option').attr('disabled', false);
                $(this).next('input').val($(this).val());
                var allValue = $('.mapping_select');
                $.each(allValue, function () {
                    $('.mapping_select option[value="' + $(this).val() + '"]').attr('disabled', true);
                });
            });
        });
    })(jQuery);
    </script>
@endsection
