@extends(route_prefix().'admin.admin-master')
@section('title') {{__('All Price Plan')}} @endsection

@section('style')
    <x-datatable.tw-css/>
<style>.hover\:text-white:hover{color:#fff!important}</style>
@endsection

@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

{{-- Table Card --}}
<div class="bg-surface rounded-xl shadow-main border border-main mb-6">

    {{-- Card Header --}}
    <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-currency-usd text-primary text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('All Price Plan')}}</h3>
                <p class="text-xs text-muted">{{__('Manage your pricing plans')}}</p>
            </div>
        </div>
        @can('price-plan-create')
        <a href="{{route(route_prefix().'admin.price.plan.create')}}"
           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition whitespace-nowrap">
            <i class="mdi mdi-plus text-base"></i>
            {{__('Create Price Plan')}}
        </a>
        @endcan
    </div>

    {{-- Table --}}
    <div class="tw-table-wrap">
        <table class="w-full text-left" id="allPricePlanTable">
            <thead>
                <tr class="border-b border-main">
                    <th class="hidden md:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest w-14 no-sort">{{__('ID')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Title')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Type')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Price')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Status')}}</th>
                    <th class="hidden sm:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Created')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest no-sort text-right">{{__('Actions')}}</th>
                </tr>
            </thead>
            <tbody>
            @foreach($all_plans as $plan)
                <tr class="border-b border-main hover:bg-muted transition-colors">

                    {{-- ID --}}
                    <td class="hidden md:table-cell px-4 py-3.5">
                        <span class="text-[11px] font-bold text-primary">{{__('#')}} {{$plan->id}}</span>
                    </td>

                    {{-- Title --}}
                    <td class="px-4 py-3.5">
                        <span class="text-sm font-semibold text-dark">{{ $plan->title }}</span>
                    </td>

                    {{-- Type --}}
                    <td class="px-4 py-3.5">
                        <span class="inline-flex items-center px-2 py-0.5 rounded bg-info-soft text-info text-[10px] font-bold uppercase">
                            {{ \App\Enums\PricePlanTypEnums::getText($plan->type) }}
                        </span>
                    </td>

                    {{-- Price --}}
                    <td class="px-4 py-3.5">
                        <span class="text-sm font-semibold text-dark">{{ amount_with_currency_symbol($plan->price) }}</span>
                    </td>

                    {{-- Status --}}
                    <td class="px-4 py-3.5">
                        @if($plan->status === \App\Enums\StatusEnums::PUBLISH)
                            <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded bg-success-soft text-success text-[10px] font-bold uppercase">
                                <i class="mdi mdi-check-circle text-[10px]"></i> {{__('Published')}}
                            </span>
                        @else
                            <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded bg-warning-soft text-warning text-[10px] font-bold uppercase">
                                <i class="mdi mdi-pencil-outline text-[10px]"></i> {{__('Draft')}}
                            </span>
                        @endif
                    </td>

                    {{-- Created --}}
                    <td class="hidden sm:table-cell px-4 py-3.5">
                        <span class="text-xs text-muted">{{$plan->created_at->format('D, d-m-y')}}</span>
                    </td>

                    {{-- Actions --}}
                    <td class="px-4 py-3.5">
                        <div class="flex items-center justify-end">
                            <div class="row-action-wrap">

                                @can('price-plan-edit')
                                <a href="{{route(route_prefix().'admin.price.plan.edit', $plan->id)}}"
                                   title="{{__('Edit')}}"
                                   class="w-9 h-9 mr-1 rounded-lg bg-primary-soft border border-main flex items-center justify-center hover:text-white hover:bg-primary hover:border-primary transition-all">
                                    <i class="mdi mdi-pencil-outline text-sm"></i>
                                </a>
                                @endcan

                                {{-- Dropdown trigger --}}
                                <button type="button" onclick="toggleRowMenu(this)"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-secondary border border-main text-dark text-xs font-semibold hover:bg-primary-soft hover:text-primary hover:border-primary transition-all">
                                    <i class="mdi mdi-dots-vertical text-sm"></i>
                                </button>

                                {{-- Dropdown panel --}}
                                <div class="row-action-menu hidden">
                                    @can('price-plan-edit')
                                    <a href="{{route(route_prefix().'admin.price.plan.edit', $plan->id)}}">
                                        <span class="action-icon bg-primary-soft"><i class="mdi mdi-pencil-outline text-primary"></i></span>
                                        {{__('Edit Plan')}}
                                    </a>
                                    @endcan

                                    <div class="menu-divider"></div>

                                    @can('price-plan-delete')
                                    <button type="button" class="action-item action-danger swal_delete_button">
                                        <span class="action-icon bg-danger-soft"><i class="mdi mdi-delete-outline text-danger"></i></span>
                                        {{__('Delete Plan')}}
                                    </button>
                                    @endcan
                                </div>
                            </div>

                            {{-- Hidden delete form --}}
                            @can('price-plan-delete')
                            <form method="post" action="{{route(route_prefix().'admin.price.plan.delete', $plan->id)}}" class="hidden d-none">
                                @csrf
                                <button type="submit" class="swal_form_submit_btn hidden d-none"></button>
                            </form>
                            @endcan
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

        // ── Row action dropdown ──────────────────────────────────────────
        window.toggleRowMenu = function (btn) {
            var menu = btn.nextElementSibling;
            var isHidden = menu.classList.contains('hidden');

            document.querySelectorAll('.row-action-menu').forEach(function (m) {
                m.classList.add('hidden');
            });

            if (isHidden) {
                var rect       = btn.getBoundingClientRect();
                var menuHeight = 320;
                var spaceBelow = window.innerHeight - rect.bottom;
                var spaceAbove = rect.top;

                menu.style.right = (window.innerWidth - rect.right) + 'px';
                menu.style.left  = 'auto';

                if (spaceBelow >= Math.min(menuHeight, 200) || spaceBelow >= spaceAbove) {
                    menu.style.top    = (rect.bottom + 4) + 'px';
                    menu.style.bottom = 'auto';
                } else {
                    menu.style.bottom = (window.innerHeight - rect.top + 4) + 'px';
                    menu.style.top    = 'auto';
                }

                menu.classList.remove('hidden');
            }
        };

        document.addEventListener('click', function (e) {
            if (!e.target.closest('.row-action-wrap')) {
                document.querySelectorAll('.row-action-menu').forEach(function (m) {
                    m.classList.add('hidden');
                });
            }
        });

        window.addEventListener('scroll', function (e) {
            if (e.target && e.target.closest && e.target.closest('.row-action-menu')) return;
            document.querySelectorAll('.row-action-menu').forEach(function (m) {
                m.classList.add('hidden');
            });
        }, true);

        // close on .tw-table-wrap horizontal scroll
        document.querySelectorAll('.tw-table-wrap').forEach(function (wrap) {
            wrap.addEventListener('scroll', function () {
                document.querySelectorAll('.row-action-menu').forEach(function (m) {
                    m.classList.add('hidden');
                });
            });
        });

        $(document).ready(function () {

            // ── DataTable init ────────────────────────────────────────
            if ($.fn.DataTable && !$.fn.dataTable.isDataTable('#allPricePlanTable')) {
                $('#allPricePlanTable').DataTable({
                    "order": [[0, "desc"]],
                    "pageLength": 10,
                    "deferRender": true,
                    "processing": true,
                    'columnDefs': [{ 'targets': 'no-sort', "orderable": false }],
                    'language': (typeof translatedDataTable === 'function') ? translatedDataTable() : {}
                });
            }

            // ── SweetAlert Delete ─────────────────────────────────────
            $(document).on('click', '.swal_delete_button', function (e) {
                e.preventDefault();
                document.querySelectorAll('.row-action-menu').forEach(function (m) { m.classList.add('hidden'); });
                let btn = $(this);
                Swal.fire({
                    title: '{{ __("Are you sure?") }}',
                    text: '{{ __("You will not be able to recover this!") }}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#989898',
                    confirmButtonText: '{{__("Yes, Delete it!")}}',
                    cancelButtonText: "{{__('Cancel')}}",
                }).then(function (result) {
                    if (result.isConfirmed) {
                        btn.closest('td').find('form').trigger('submit');
                    }
                });
            });

            $(document).on('change','select[name="lang"]',function (e){
                $(this).closest('form').trigger('submit');
                $('input[name="lang"]').val($(this).val());
            });
        });
    })(jQuery);
    </script>
@endsection
