{{-- NEW FOOTER START --}}
{{--<footer class="bg-cyan-50 pt-[120px] pb-8">--}}
{{--    <div class="container mx-auto px-8">--}}
{{--        --}}{{-- Row 1: Logo + Nav Links --}}
{{--        <div class="grid grid-cols-1 sm:grid-cols-6 lg:grid-cols-12 gap-8 lg:gap-12 mb-12">--}}
{{--            {!! render_frontend_sidebar('footer', ['column' => false]) !!}--}}
{{--        </div>--}}

{{--        --}}{{-- Row 2: Social + Newsletter --}}
{{--        <div class="grid grid-cols-1 md:grid-cols-6 lg:grid-cols-12 gap-12 justify-between mb-16">--}}
{{--            {!! render_frontend_sidebar('footer_bottom', ['column' => false]) !!}--}}
{{--        </div>--}}

{{--        --}}{{-- Copyright --}}
{{--        <div class="border-t border-gray-300 pt-6 flex flex-col sm:flex-row justify-between items-center gap-4">--}}
{{--            <p class="text-gray-600 text-sm">{!! get_footer_copyright_text() !!}</p>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</footer>--}}

{{--<footer class="bg-cyan-50 pt-[60px] sm:pt-[80px] lg:pt-[120px] pb-8">--}}
{{--    <div class="container mx-auto px-4 sm:px-6 lg:px-8">--}}
{{--        --}}{{-- Row 1: Logo + Nav Links --}}
{{--        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-12 gap-6 sm:gap-8 lg:gap-12 mb-8 sm:mb-10 lg:mb-12 p-1">--}}
{{--            @php--}}
{{--                $footerWidgets = render_frontend_sidebar('footer', ['column' => false, 'return_array' => true]);--}}
{{--            @endphp--}}
{{--            @foreach($footerWidgets as $widget)--}}
{{--                <div class="w-full break-words">--}}
{{--                    {!! $widget !!}--}}
{{--                </div>--}}
{{--            @endforeach--}}
{{--        </div>--}}
{{--        --}}{{-- Row 2: Social + Newsletter --}}
{{--        <div class="flex flex-col sm:flex-row justify-between items-center gap-8 md:gap-10 lg:gap-12 mb-10 md:mb-12 lg:mb-16">--}}
{{--            @php--}}
{{--                $footerBottomWidgets = render_frontend_sidebar('footer_bottom', ['column' => false, 'return_array' => true]);--}}
{{--            @endphp--}}
{{--            @foreach($footerBottomWidgets as $widget)--}}
{{--                <div class="w-full sm:w-auto break-words">--}}
{{--                    {!! $widget !!}--}}
{{--                </div>--}}
{{--            @endforeach--}}
{{--        </div>--}}

{{--        --}}{{-- Copyright --}}
{{--        <div class="border-t border-gray-300 pt-6 flex flex-col sm:flex-row justify-between items-center gap-4 text-center sm:text-left">--}}
{{--            <p class="text-gray-600 text-xs sm:text-sm">{!! get_footer_copyright_text() !!}</p>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</footer>--}}
{{-- NEW FOOTER END --}}

<footer class="pt-[60px] sm:pt-[80px] lg:pt-[120px] pb-8" style="background-color: var(--section-bg-6, #E5EFF8)">
    <div class="container mx-auto px-8 sm:px-6 lg:px-8">

        {{-- Row 1: Logo + Nav Links (widgets control their own col-span) --}}
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-12 gap-6 sm:gap-8 lg:gap-12 mb-10 lg:mb-14">
            {!! render_frontend_sidebar('footer', ['column' => false]) !!}
        </div>

        {{-- Row 2: Social + Newsletter (same grid as Row 1 for perfect column alignment) --}}
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-12 gap-6 sm:gap-8 lg:gap-12 mb-12 lg:mb-16">
            {!! render_frontend_sidebar('footer_bottom', ['column' => false]) !!}
        </div>

        {{-- Copyright bar --}}
        <div class="border-t pt-6 flex flex-col sm:flex-row justify-between items-center gap-4 text-center sm:text-left" style="border-color: rgba(var(--main-color-one-rgb, 240, 72, 83), 0.15)">
            <p class="text-xs sm:text-sm" style="color: var(--body-color, #666666)">{!! get_footer_copyright_text() !!}</p>
            <div class="flex items-center gap-1 flex-wrap justify-center sm:justify-end">
                {!! render_frontend_sidebar('footer_copyright') !!}
            </div>
        </div>
        @if(get_static_option_central('captcha_status') == 'on')
        <p class="lg:text-right md:text-right text-center pt-1" style="font-size:10px;color:#aaa;">
            This site is protected by reCAPTCHA and the Google
            <a href="https://policies.google.com/privacy" target="_blank" style="color:#aaa;text-decoration:underline;">Privacy Policy</a> and
            <a href="https://policies.google.com/terms" target="_blank" style="color:#aaa;text-decoration:underline;">Terms of Service</a> apply.
        </p>
        @endif

    </div>
</footer>


<div class="back-to-top">
    <span class="back-top"> <i class="las la-angle-up"></i> </span>
</div>

{{--<script src="{{asset('assets/landlord/frontend/js/jquery-3.6.1.min.js')}}"></script>--}}
{{--<script src="{{asset('assets/landlord/frontend/js/jquery-migrate-3.4.0.min.js')}}"></script>--}}
{{--<script src="{{asset('assets/landlord/frontend/js/jquery.lazy.min.js')}}"></script>--}}
{{--<script src="{{asset('assets/landlord/frontend/js/bootstrap.bundle.min.js')}}"></script>--}}
{{--<script src="{{asset('assets/landlord/common/js/axios.min.js')}}"></script>--}}
{{--<script src="{{asset('assets/landlord/frontend/js/slick.js')}}"></script>--}}
{{--<script src="{{asset('assets/landlord/frontend/js/odometer.js')}}"></script>--}}
{{--<script src="{{asset('assets/landlord/frontend/js/wow.js')}}"></script>--}}
{{--<script src="{{asset('assets/landlord/frontend/js/viewport.jquery.js')}}"></script>--}}
{{--<script src="{{asset('assets/landlord/frontend/js/jquery.syotimer.min.js')}}"></script>--}}
{{--<script src="{{asset('assets/landlord/frontend/js/jquery.nice-select.js')}}"></script>--}}
{{--<script src="{{asset('assets/landlord/frontend/js/jquery.nicescroll.min.js')}}"></script>--}}
{{--<script src="{{asset('assets/landlord/frontend/js/nouislider-8.5.1.min.js')}}"></script>--}}
{{--<script src="{{asset('assets/landlord/frontend/js/main.js')}}"></script>--}}


{{-- new landlord page --}}

<script src="{{asset('assets/new-landlord/js/plugin.js')}}"></script>
<script src="{{asset('assets/common/js/toastr.min.js')}}"></script>
<x-custom-js.contact-form-store/>
<script>
$(document).on('submit', '.contact-two-form', function (e) {
    e.preventDefault();
    var form       = $(this);
    var btn        = form.find('.ct-submit-btn');
    var msgBox     = form.find('.ct-error');
    var successMsg = form.data('success') || '{{ __("Thanks for your message!") }}';

    msgBox.html('');

    $.ajax({
        url:  form.attr('action'),
        type: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        data: form.serialize(),
        beforeSend: function () {
            btn.html('<i class="fas fa-spinner fa-spin"></i> {{ __("Sending...") }}');
            btn.prop('disabled', true);
        },
        success: function (data) {
            var cls = data.type === 'success' ? 'alert-success' : 'alert-danger';
            msgBox.html('<div class="alert ' + cls + '">' + data.msg + '</div>');
            if (data.type === 'success') { form[0].reset(); }
            btn.text('{{ __("Send Message") }}');
            btn.prop('disabled', false);
            $('html, body').animate({ scrollTop: msgBox.offset().top - 120 }, 400);
        },
        error: function (data) {
            var markup = '<ul class="alert alert-danger">';
            if (data.responseJSON && data.responseJSON.errors) {
                $.each(data.responseJSON.errors, function (field, messages) {
                    $.each(Array.isArray(messages) ? messages : [messages], function (i, msg) {
                        markup += '<li>' + msg + '</li>';
                    });
                });
            } else {
                markup += '<li>{{ __("Something went wrong. Please try again.") }}</li>';
            }
            markup += '</ul>';
            msgBox.html(markup);
            btn.text('{{ __("Send Message") }}');
            btn.prop('disabled', false);
            $('html, body').animate({ scrollTop: msgBox.offset().top - 120 }, 400);
        }
    });
});
</script>
<script src="{{asset('assets/new-landlord/js/counter_up.js')}}"></script>
<script src="{{asset('assets/new-landlord/js/glightbox.min.js')}}"></script>
<script src="{{asset('assets/new-landlord/js/pagination_sliders.js')}}"></script>
<script src="{{asset('assets/new-landlord/js/pagination_sliders_2.js')}}"></script>
<script src="{{asset('assets/new-landlord/js/theme_sliders.js')}}"></script>
<script src="{{asset('assets/new-landlord/js/unique_sliders.js')}}"></script>
<script src="{{asset('assets/new-landlord/js/accoredian.js')}}"></script>
<script src="{{asset('assets/new-landlord/js/accoredian.js')}}"></script>
<script src="{{asset('assets/new-landlord/js/nav.js')}}"></script>
<script src="{{asset('assets/new-landlord/js/feedback.js')}}"></script>

{{--<script src="{{asset('assets/new-landlord/js/allNav.js')}}"></script>--}}
<script src="{{asset('assets/new-landlord/js/activePage.js')}}"></script>



<script src="{{asset('assets/new-landlord/js/main.js')}}"></script>



@include('landlord.frontend.partials.gdpr-cookie')

<script>
    $.ajaxSetup({
        headers: {
            'X-XSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
</script>
<x-custom-js.lang-change-landlord/>
<x-custom-js.landlord-newsletter-store/>
<x-custom-js.lazy-load-image/>

<script>
    $(function () {
        var ENDPOINT = window.location.href;
        var posts = 6;

        $(document).on('click', '#load_more', function (e) {
            e.preventDefault();
            var el = $(this);
            var category = el.data('category');
            var order = el.data('order');
            var order_by = el.data('order_by');

            posts += 6;
            LoadMore(posts, category, order, order_by);
        });

        function LoadMore(posts, category, order, order_by) {
            $.ajax({
                url: '{{route('landlord.frontend.blog.load_more.ajax')}}',
                type: "get",
                data: {
                    'posts': posts,
                    'category': category,
                    'order': order,
                    'order_by': order_by
                },
                beforeSend: function () {
                    $('#load_more').text('Loading..');
                },
                success: function (response) {
                    if (response != '') {
                        let load_more_items = $("#load-more-items");
                        load_more_items.css('display', 'none');
                        load_more_items.fadeIn(1000);
                        load_more_items.append(response);

                        $('#load_more').text('Load More');
                    } else {
                        $('#load_more').text('No More Blog Available');
                    }
                },
                error: function (jqXHR, ajaxOptions, thrownError) {

                }
            });
        }
    });
</script>

<script>
    if (window.top != window.self) {
        document.body.innerHTML += '<div class="external-website">' +
            '<p class="external-website-para">{{ __('You are using this website under an external iframe!!') }}</p>' +
            '<p  class="external-website-para mt-3">{{ __('for a better experience, please browse directly instead of an external iframe.') }}</p>' +
            '<a href="' + window.self.location + '" target="_blank" class="external-website-btn mt-3">{{ __('Browse Directly') }}</a>' +
            '</div>';
    }
</script>

    @yield('scripts')

    @php
        $dynamic_script = 'assets/landlord/frontend/js/dynamic-script.js';
    @endphp
    @if(file_exists($dynamic_script))
        <script src="{{asset($dynamic_script)}}"></script>
    @endif
    {!! get_static_option('site_third_party_tracking_code') !!}
    {!! renderBodyEndHooks() !!}
    @include('landlord.frontend.partials.purchase-modal')
</body>
</html>
