@extends(route_prefix().'admin.admin-master')
@section('title') {{__('Order Report')}} @endsection

@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

<div class="space-y-6">

    {{-- Filter Card --}}
    <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">
        <div class="px-4 sm:px-6 py-4 border-b border-main flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-chart-bar text-primary text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Order Report')}}</h3>
                <p class="text-xs text-muted">{{__('Filter and generate order reports')}}</p>
            </div>
        </div>

        <div class="p-4 sm:p-6">
            <form action="{{route(route_prefix().'admin.product.order.report')}}" method="get" enctype="multipart/form-data" id="report_generate_form">
                <input type="hidden" name="page" value="1">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">

                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Start Date')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                            <i class="mdi mdi-calendar-start text-lg text-primary"></i>
                            <input type="date" name="start_date" value="{{$start_date}}"
                                   class="flex-1 bg-transparent text-sm text-dark outline-none border-none focus:ring-0 p-0">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('End Date')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                            <i class="mdi mdi-calendar-end text-lg text-primary"></i>
                            <input type="date" name="end_date" value="{{$end_date}}"
                                   class="flex-1 bg-transparent text-sm text-dark outline-none border-none focus:ring-0 p-0">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Status')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-1 focus-within:border-primary transition">
                            <i class="mdi mdi-list-status text-lg text-primary"></i>
                            <select name="order_status" class="flex-1 bg-transparent text-sm text-dark outline-none border-none focus:ring-0 p-0 appearance-none cursor-pointer">
                                <option value="">{{__('All')}}</option>
                                <option @if($order_status == 'pending') selected @endif value="pending">{{__('Pending')}}</option>
                                <option @if($order_status == 'completed') selected @endif value="completed">{{__('Completed')}}</option>
                                <option @if($order_status == 'in_progress') selected @endif value="in_progress">{{__('In Progress')}}</option>
                            </select>
                            <i class="mdi mdi-chevron-down text-base text-primary pointer-events-none"></i>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Payment Status')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-1 focus-within:border-primary transition">
                            <i class="mdi mdi-cash-check text-lg text-primary"></i>
                            <select name="payment_status" class="flex-1 bg-transparent text-sm text-dark outline-none border-none focus:ring-0 p-0 appearance-none cursor-pointer">
                                <option value="">{{__('All')}}</option>
                                <option @if($payment_status == 'pending') selected @endif value="pending">{{__('Pending')}}</option>
                                <option @if($payment_status == 'completed') selected @endif value="completed">{{__('Completed')}}</option>
                            </select>
                            <i class="mdi mdi-chevron-down text-base text-primary pointer-events-none"></i>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Items')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-1 focus-within:border-primary transition">
                            <i class="mdi mdi-format-list-numbered text-lg text-primary"></i>
                            <select name="items" class="flex-1 bg-transparent text-sm text-dark outline-none border-none focus:ring-0 p-0 appearance-none cursor-pointer">
                                <option @if($items == '10') selected @endif value="10">{{__('10')}}</option>
                                <option @if($items == '20') selected @endif value="20">{{__('20')}}</option>
                                <option @if($items == '50') selected @endif value="50">{{__('50')}}</option>
                            </select>
                            <i class="mdi mdi-chevron-down text-base text-primary pointer-events-none"></i>
                        </div>
                    </div>

                    <div class="flex items-end gap-2">
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                            <i class="mdi mdi-magnify text-base"></i> {{__('Submit')}}
                        </button>
                        @if(!empty($order_data) && count($order_data) > 0)
                        <button type="button" id="download_as_csv"
                                class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-secondary border border-main text-dark text-sm font-semibold hover:bg-primary-soft hover:text-primary hover:border-primary transition">
                            <i class="mdi mdi-download text-base"></i> {{__('CSV')}}
                        </button>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Results Table --}}
    @if(!empty($order_data))
    <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">
        <div class="px-4 sm:px-6 py-4 border-b border-main">
            <h4 class="text-xs font-bold text-dark uppercase tracking-widest">{{__('Report Results')}}</h4>
        </div>

        @if(count($order_data) > 0)
        <div class="tw-table-wrap">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-main">
                        <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Order ID')}}</th>
                        <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Package Name')}}</th>
                        <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Package Price')}}</th>
                        <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Payment Status')}}</th>
                        <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Order Status')}}</th>
                        <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Date')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order_data as $data)
                    <tr class="border-b border-main hover:bg-muted transition-colors">
                        <td class="px-4 py-3.5">
                            <span class="text-[11px] font-bold text-primary">{{__('#')}} {{$data->id}}</span>
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="text-sm font-semibold text-dark">{{$data->package_name}}</span>
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="text-sm font-semibold text-dark">{{amount_with_currency_symbol($data->package_price)}}</span>
                        </td>
                        <td class="px-4 py-3.5">
                            @if($data->payment_status == 'pending')
                                <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded bg-warning-soft text-warning text-[10px] font-bold uppercase">
                                    <i class="mdi mdi-clock-outline text-[10px]"></i> {{__($data->payment_status)}}
                                </span>
                            @else
                                <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded bg-success-soft text-success text-[10px] font-bold uppercase">
                                    <i class="mdi mdi-check-circle text-[10px]"></i> {{__($data->payment_status)}}
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5">
                            @if($data->status == 'pending')
                                <span class="inline-flex items-center px-2 py-0.5 rounded bg-warning-soft text-warning text-[10px] font-bold uppercase">{{__($data->status)}}</span>
                            @elseif($data->status == 'canceled')
                                <span class="inline-flex items-center px-2 py-0.5 rounded bg-danger-soft text-danger text-[10px] font-bold uppercase">{{__($data->status)}}</span>
                            @elseif($data->status == 'in_progress')
                                <span class="inline-flex items-center px-2 py-0.5 rounded bg-info-soft text-info text-[10px] font-bold uppercase">{{__(str_replace('_',' ',$data->status))}}</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded bg-success-soft text-success text-[10px] font-bold uppercase">{{__($data->status)}}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="text-xs text-muted">{{date_format($data->created_at,'d M Y')}}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="px-4 sm:px-6 py-4 border-t border-main report-pagination">
            {!! $order_data->links() !!}
        </div>
        @else
            <div class="p-6">
                <div class="flex items-center gap-3 bg-warning-soft border border-yellow-200 rounded-xl px-4 py-4">
                    <i class="mdi mdi-alert-circle-outline text-warning text-xl"></i>
                    <span class="text-sm text-dark">{{__('No Item Found')}}</span>
                </div>
            </div>
        @endif
    </div>
    @endif

</div>

@endsection

@section('scripts')
    <script>
    (function ($) {
        "use strict";
        $(document).ready(function () {
            $(document).on('click', '.report-pagination nav ul li a', function (e) {
                e.preventDefault();
                var el = $(this);
                var href = el.attr('href');
                var match = href.match(/(:?=)\d+/);
                var pageNumber = match != null ? match[0].replace('=', ' ') : '';
                $('input[name="page"]').val(pageNumber.trim());
                $('#report_generate_form').trigger('submit');
            });

            $(document).on('click', '#download_as_csv', function (e) {
                e.preventDefault();
                exportTableToCSV('product-order-report.csv');
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
                var rows = document.querySelectorAll("table tr");
                for (var i = 0; i < rows.length; i++) {
                    var row = [], cols = rows[i].querySelectorAll("td, th");
                    for (var j = 0; j < cols.length; j++)
                        row.push(cols[j].innerText);
                    csv.push(row.join(","));
                }
                downloadCSV(csv.join("\n"), filename);
            }
        });
    })(jQuery);
    </script>
@endsection
