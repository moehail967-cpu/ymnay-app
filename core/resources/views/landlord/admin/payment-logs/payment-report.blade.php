@extends(route_prefix().'admin.admin-master')
@section('title') {{__('Payment Report')}} @endsection

@section('style')
    <x-datatable.tw-css/>
    <style>
        .report-pagination nav ul {
            display: flex;
            flex-wrap: wrap;
            gap: 0.25rem;
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .report-pagination nav ul li a,
        .report-pagination nav ul li span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 2rem;
            height: 2rem;
            padding: 0 0.5rem;
            font-size: 0.8125rem;
            border-radius: 0.5rem;
            border: 1px solid var(--color-border-main, #e5e7eb);
            color: var(--color-text-main, #1a1816);
            background: var(--color-bg-surface, #fff);
            text-decoration: none;
            transition: all 0.15s;
        }
        .report-pagination nav ul li a:hover {
            background: var(--color-bg-muted, #f3f4f6);
            border-color: var(--color-primary, #1a5c4e);
            color: var(--color-primary, #1a5c4e);
        }
        .report-pagination nav ul li.active span,
        .report-pagination nav ul li span[aria-current="page"] {
            background: var(--color-primary, #1a5c4e);
            border-color: var(--color-primary, #1a5c4e);
            color: #fff;
        }
        .report-pagination nav ul li.disabled span {
            opacity: 0.4;
            cursor: not-allowed;
        }
    </style>
@endsection

@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

{{-- Filter Card --}}
<div class="bg-surface rounded-xl shadow-main border border-main mb-6">

    {{-- Card Header --}}
    <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-filter-outline text-primary text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Payment Report')}}</h3>
                <p class="text-xs text-muted">{{__('Generate and export payment reports')}}</p>
            </div>
        </div>
    </div>

    {{-- Filter Form --}}
    <form action="{{route(route_prefix().'admin.payment.report')}}" method="get" enctype="multipart/form-data" id="report_generate_form">
        <input type="hidden" name="page" value="1">

        <div class="p-4 sm:p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">

                {{-- Start Date --}}
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Start Date')}}</label>
                    <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                        <i class="mdi mdi-calendar-start text-lg text-primary"></i>
                        <input type="date" name="start_date" value="{{$start_date}}"
                               class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                    </div>
                </div>

                {{-- End Date --}}
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('End Date')}}</label>
                    <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                        <i class="mdi mdi-calendar-end text-lg text-primary"></i>
                        <input type="date" name="end_date" value="{{$end_date}}"
                               class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                    </div>
                </div>

                {{-- Payment Status --}}
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Payment Status')}}</label>
                    <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2 focus-within:border-primary transition">
                        <i class="mdi mdi-list-status text-lg text-primary"></i>
                        <select name="payment_status" id="order_status"
                                class="flex-1 bg-transparent text-sm text-dark outline-none border-none focus:ring-0 p-0 appearance-none cursor-pointer">
                            <option value="">{{__('All')}}</option>
                            <option @if($payment_status == 'pending') selected @endif value="pending">{{__('Pending')}}</option>
                            <option @if($payment_status == 'complete') selected @endif value="complete">{{__('Complete')}}</option>
                        </select>
                        <i class="mdi mdi-chevron-down text-base text-primary pointer-events-none"></i>
                    </div>
                </div>

                {{-- Items Per Page --}}
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Items')}}</label>
                    <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2 focus-within:border-primary transition">
                        <i class="mdi mdi-format-list-numbered text-lg text-primary"></i>
                        <select name="items" id="items"
                                class="flex-1 bg-transparent text-sm text-dark outline-none border-none focus:ring-0 p-0 appearance-none cursor-pointer">
                            <option @if($items == '10') selected @endif value="10">{{__('10')}}</option>
                            <option @if($items == '20') selected @endif value="20">{{__('20')}}</option>
                            <option @if($items == '50') selected @endif value="50">{{__('50')}}</option>
                        </select>
                        <i class="mdi mdi-chevron-down text-base text-primary pointer-events-none"></i>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-2 pt-1">
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition whitespace-nowrap">
                        <i class="mdi mdi-magnify text-base"></i>
                        {{__('Generate')}}
                    </button>
                    @if(!empty($order_data) && count($order_data) > 0)
                        <button type="button" id="download_as_csv"
                                class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-secondary border border-main text-dark text-sm font-semibold hover:bg-primary-soft hover:text-primary hover:border-primary transition whitespace-nowrap">
                            <i class="mdi mdi-download text-base"></i>
                            {{__('CSV')}}
                        </button>
                    @endif
                </div>

            </div>
        </div>
    </form>

</div>

{{-- Results Table Card --}}
@if(!empty($order_data))
<div class="bg-surface rounded-xl shadow-main border border-main mb-6">

    {{-- Card Header --}}
    <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-success-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-table-large text-success text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Report Results')}}</h3>
                <p class="text-xs text-muted">{{__('Showing')}} {{count($order_data)}} {{__('records')}}</p>
            </div>
        </div>
    </div>

    @if(count($order_data) > 0)
    {{-- Table --}}
    <div class="tw-table-wrap">
        <table class="w-full text-left" id="paymentReportTable">
            <thead>
                <tr class="border-b border-main">
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest w-14">{{__('Order ID')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Billing Info')}}</th>
                    <th class="hidden md:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Package')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Amount')}}</th>
                    <th class="hidden sm:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Gateway')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Status')}}</th>
                    <th class="hidden lg:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Transaction ID')}}</th>
                    <th class="hidden sm:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Date')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order_data as $data)
                <tr class="border-b border-main hover:bg-muted transition-colors">

                    {{-- Order ID --}}
                    <td class="px-4 py-3.5">
                        <span class="text-[11px] font-bold text-primary">{{__('#')}} {{$data->id}}</span>
                    </td>

                    {{-- Billing Info --}}
                    <td class="px-4 py-3.5">
                        <div class="tw-cell-user">
                            <div class="tw-avatar-initials">
                                {{ strtoupper(substr($data->name ?? 'U', 0, 1)) }}
                            </div>
                            <div>
                                <div class="tw-cell-name">{{$data->name}}</div>
                                <div class="tw-cell-sub">{{$data->email}}</div>
                            </div>
                        </div>
                    </td>

                    {{-- Package Name --}}
                    <td class="hidden md:table-cell px-4 py-3.5">
                        <span class="text-sm font-semibold text-dark">{{$data->package_name}}</span>
                    </td>

                    {{-- Amount --}}
                    <td class="px-4 py-3.5">
                        <span class="text-sm font-bold text-dark">{{amount_with_currency_symbol($data->package_price)}}</span>
                    </td>

                    {{-- Payment Gateway --}}
                    <td class="hidden sm:table-cell px-4 py-3.5">
                        <span class="tw-pill tw-pill-info">
                            <i class="mdi mdi-credit-card-outline text-[10px] mr-0.5"></i>
                            {{ucwords(str_replace('_', ' ', $data->package_gateway))}}
                        </span>
                    </td>

                    {{-- Status --}}
                    <td class="px-4 py-3.5">
                        @if($data->status == 'pending')
                            <span class="tw-pill tw-pill-warning">
                                <i class="mdi mdi-clock-outline text-[10px] mr-0.5"></i> {{__('Pending')}}
                            </span>
                        @else
                            <span class="tw-pill tw-pill-success">
                                <i class="mdi mdi-check-circle text-[10px] mr-0.5"></i> {{__('Complete')}}
                            </span>
                        @endif
                    </td>

                    {{-- Transaction ID --}}
                    <td class="hidden lg:table-cell px-4 py-3.5">
                        @if($data->transaction_id)
                            <span class="text-xs font-mono text-muted bg-secondary px-2 py-1 rounded-lg">{{$data->transaction_id}}</span>
                        @else
                            <span class="text-xs text-muted italic">{{__('N/A')}}</span>
                        @endif
                    </td>

                    {{-- Date --}}
                    <td class="hidden sm:table-cell px-4 py-3.5">
                        <span class="text-xs text-muted">{{date_format($data->created_at, 'd M Y')}}</span>
                    </td>

                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="px-4 sm:px-6 py-4 border-t border-main report-pagination">
        {!! $order_data->links() !!}
    </div>

    @else
        <div class="p-6">
            <div class="flex flex-col items-center gap-2 py-8">
                <div class="w-12 h-12 rounded-full bg-secondary flex items-center justify-center">
                    <i class="mdi mdi-file-search-outline text-muted text-xl"></i>
                </div>
                <span class="text-sm text-muted">{{__('No items found for the selected criteria')}}</span>
            </div>
        </div>
    @endif

</div>
@endif

@endsection

@section('scripts')
    <script>
    (function ($) {
        "use strict";
        $(document).ready(function () {

            // ── Pagination ──────────────────────────────────────────
            $(document).on('click', '.report-pagination nav ul li a', function (e) {
                e.preventDefault();
                var el = $(this);
                var href = el.attr('href');
                var match = href.match(/(:?=)\d+/);
                var pageNumber = match != null ? match[0].replace('=', ' ') : '';
                $('input[name="page"]').val(pageNumber.trim());
                $('#report_generate_form').trigger('submit');
            });

            // ── CSV Export ──────────────────────────────────────────
            $(document).on('click', '#download_as_csv', function (e) {
                e.preventDefault();
                exportTableToCSV('payment-logs-report.csv');
            });

            function downloadCSV(csv, filename) {
                var csvFile = new Blob([csv], { type: "text/csv" });
                var downloadLink = document.createElement("a");
                downloadLink.download = filename;
                downloadLink.href = window.URL.createObjectURL(csvFile);
                downloadLink.style.display = "none";
                document.body.appendChild(downloadLink);
                downloadLink.click();
                document.body.removeChild(downloadLink);
            }

            function exportTableToCSV(filename) {
                var csv = [];
                var rows = document.querySelectorAll("#paymentReportTable tr");
                for (var i = 0; i < rows.length; i++) {
                    var row = [], cols = rows[i].querySelectorAll("td, th");
                    for (var j = 0; j < cols.length; j++)
                        row.push('"' + cols[j].innerText.replace(/"/g, '""') + '"');
                    csv.push(row.join(","));
                }
                downloadCSV(csv.join("\n"), filename);
            }

        });
    })(jQuery);
    </script>
@endsection
