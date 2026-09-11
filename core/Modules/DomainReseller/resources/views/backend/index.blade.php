@extends(route_prefix().'admin.admin-master')

@section('title')
    {{ __('Domain Reseller Plugin') }}
@endsection

@section('content')

<x-landlord-error-msg/>
<x-landlord-flash-msg/>

<div class="bg-surface rounded-xl shadow-main border border-main mb-6">
    <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                <i class="las la-globe text-primary text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Purchase Domain')}}</h3>
                <p class="text-xs text-muted">{{__('Find your perfect domain just searching domain name')}}</p>
            </div>
        </div>
        <a href="{{route(route_prefix().'admin.domain-reseller.list.domain')}}"
           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg border border-main text-xs font-semibold text-muted hover:text-dark hover:border-sidebar-hover transition">
            <i class="las la-list"></i>
            {{tenant() ? __('My Domains') : __('All Domains')}}
        </a>
    </div>

    <div class="px-4 sm:px-6 py-5">
        <form class="search-domain-form">
            <div class="dr-search-wrap">
                <input type="text" class="dr-search-input" id="domain_name" name="domain_name"
                       placeholder="{{__('Find your perfect domain...')}}">
                <button class="dr-search-btn" type="submit">
                    <i class="las la-search"></i>
                    {{__('Search Domain')}}
                    <span class="cm-spinner hidden"></span>
                </button>
            </div>
            <p class="dr-field-error domain-error-msg hidden mt-2"></p>
        </form>

        <div class="domain-availability-wrapper hidden mt-6"></div>
        <div class="suggested-domains-wrapper hidden mt-6"></div>
    </div>
</div>

@endsection

@section('scripts')
    <script>
        (function ($) {
            "use strict";

            const toggleSpinner = (btn, show) => {
                btn.find('.cm-spinner').toggleClass('hidden', !show);
                btn.attr('disabled', show);
            };

            $(document).on('submit', 'form.search-domain-form', function (e) {
                e.preventDefault();

                let form = $(this);
                let btn = form.find('button[type=submit]');
                let domain_el = $('#domain_name');
                let domain_text = domain_el.val().trim().toLowerCase();
                let errorEl = form.find('.domain-error-msg');

                errorEl.addClass('hidden').text('');
                domain_el.removeClass('dr-has-error');

                if (!domain_text) {
                    domain_el.addClass('dr-has-error');
                    errorEl.removeClass('hidden').text(`{{__('The domain name field is required.')}}`);
                    toastr.error(`{{__('The domain name field is required.')}}`);
                    return false;
                }

                $.ajax({
                    type: 'POST',
                    url: `{{route(route_prefix().'admin.domain-reseller.search.domain')}}`,
                    data: {
                        _token: `{{csrf_token()}}`,
                        domain_name: domain_text
                    },
                    beforeSend: function () {
                        toggleSpinner(btn, true);
                    },
                    success: function (response) {
                        toggleSpinner(btn, false);

                        if (!response.status) {
                            domain_el.addClass('dr-has-error');
                            errorEl.removeClass('hidden').text(response.message);
                            toastr.error(response.message);
                            checkException(response);
                            return false;
                        }

                        let wrapper = $('.domain-availability-wrapper');
                        wrapper.removeClass('hidden').html('');

                        let domain = response.result.domain;
                        let lastDot = domain.lastIndexOf('.');
                        let domainName = domain.substring(0, lastDot);

                        if (response.result.available) {
                            let sym = response.result.currency === 'USD' ? '$' : response.result.currency;
                            let price = response.result.price;

                            wrapper.html(`
                                <p class="text-sm font-semibold text-success mb-3"><i class="las la-check-circle mr-1"></i> ${domain} {{__('is available')}}</p>
                                <div class="dr-result-card">
                                    <div class="flex items-center justify-between mb-4">
                                        <span class="dr-badge dr-badge-primary">{{__('EXACT MATCH')}}</span>
                                        <label class="dr-toggle">
                                            <input type="checkbox" id="request-privacy" checked>
                                            <span class="dr-toggle-track"></span>
                                            <span>{{__('Privacy Protection')}}</span>
                                        </label>
                                    </div>
                                    <h2 class="dr-domain-name">${domain}</h2>
                                    <p class="dr-price">${sym}${price}</p>
                                    <p class="text-xs text-muted mb-4">{{__('Validity')}} ${response.result.period} {{__('Year')}}</p>
                                    <button class="buy-button dr-buy-btn" data-domain="${domain}">
                                        {{__('Make It Yours')}}
                                        <span class="cm-spinner hidden"></span>
                                    </button>
                                    <p class="dr-hint">
                                        <i class="las la-lightbulb"></i>
                                        {{__('Why it\'s great:')}} "${domainName}" {{__('is')}} ${domainName.length} {{__('characters or less.')}}
                                    </p>
                                </div>
                            `);
                        } else {
                            wrapper.html(`
                                <p class="text-sm font-semibold text-danger mb-3"><i class="las la-times-circle mr-1"></i> ${domain} {{__('is not available')}}</p>
                                <div class="dr-result-card">
                                    <span class="dr-badge dr-badge-muted mb-3">{{__('EXACT MATCH')}}</span>
                                    <h2 class="dr-domain-name">${domain}</h2>
                                    <p class="text-muted text-sm">{{__('This domain is already taken')}}</p>
                                    <p class="dr-hint">"${domainName}" {{__('is')}} ${domainName.length} {{__('characters or less.')}}</p>
                                </div>
                            `);
                        }

                        let isLandlord = `{{empty(tenant())}}`;
                        if (isLandlord) {
                            toastr.warning(`{{__('This service is only available for tenants (shops). You can use this for test purpose.')}}`);
                            $(".dr-result-card *").attr("disabled", true).attr('data-domain', '').off('click');
                        }
                    },
                    error: function () {
                        toggleSpinner(btn, false);
                        toastr.error('{{__("Something went wrong")}}');
                    }
                });
            });

            const checkException = (response) => {
                if (response.exception) {
                    let list = response.suggestion;
                    let wrapper = $('.suggested-domains-wrapper');
                    wrapper.removeClass('hidden').html('');

                    let grid = $('<div class="dr-suggestion-grid"></div>');
                    list.forEach((item) => {
                        grid.append(`
                            <div class="dr-suggestion-card">
                                <span class="dr-badge dr-badge-muted mb-2">{{__('SUGGESTION')}}</span>
                                <h3 class="text-base font-bold text-dark mb-3">${item.domain}</h3>
                                <button class="buy-button dr-buy-btn text-xs !px-4 !py-2" data-domain="${item.domain}">
                                    {{__('Make It Yours')}}
                                </button>
                                <p class="text-xs text-muted mt-2">${item.domain.length} {{__('characters')}}</p>
                            </div>
                        `);
                    });
                    wrapper.append(grid);
                }
            };

            $(document).on('click', '.buy-button', function () {
                let el = $(this);
                let domain_name = el.attr('data-domain')?.trim();
                let privacy_request = $('#request-privacy').is(':checked');

                if (domain_name && domain_name !== "" && isNaN(domain_name)) {
                    $.ajax({
                        type: 'POST',
                        url: `{{route('tenant.admin.domain-reseller.select.domain')}}`,
                        data: {
                            _token: `{{csrf_token()}}`,
                            domain_name,
                            privacy_request,
                        },
                        beforeSend: function () {
                            el.find('.cm-spinner').removeClass('hidden');
                            el.attr('disabled', true);
                        },
                        success: function (response) {
                            if (response.status) {
                                setTimeout(() => { location.href = response.url; }, 2000);
                            } else if (response.type !== undefined && response.type === 'warning') {
                                toastr.warning(response.msg);
                                el.find('.cm-spinner').addClass('hidden');
                                el.attr('disabled', false);
                            } else {
                                toastr.error('{{__("Something went wrong")}}');
                                el.find('.cm-spinner').addClass('hidden');
                                el.attr('disabled', false);
                            }
                        }
                    });
                }
            });
        })(jQuery);
    </script>
@endsection
