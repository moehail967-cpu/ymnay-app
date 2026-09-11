<!-- Footer -->
<footer class="border-t border-main px-4 sm:px-6 py-4 flex-shrink-0 bg-surface/60">
    <div class="flex flex-col sm:flex-row justify-between items-center gap-2 text-sm text-subtle">
        <span>{!! get_footer_copyright_text() !!}</span>
        <span>v-<strong class="text-main">{{get_static_option_central('get_script_version')}}</strong></span>
    </div>
</footer>

</div> {{-- end admin-content --}}
</div> {{-- end flex h-screen --}}

<!-- Scripts: keep all existing JS -->
<script src="{{global_asset('assets/landlord/admin/js/landlord.bundle.base.js')}}"></script>
<script src="{{global_asset('assets/landlord/common/js/axios.min.js')}}"></script>
<script src="{{global_asset('assets/landlord/common/js/sweetalert2.js')}}"></script>
<script src="{{global_asset('assets/common/js/countdown.jquery.js')}}"></script>
<script src="{{global_asset('assets/common/js/flatpickr.js')}}"></script>
<x-flatpicker.flatpickr-locale/>
<script src="{{global_asset('assets/common/js/toastr.min.js')}}"></script>
<script src="{{global_asset('assets/common/js/select2.min.js')}}"></script>
<script src="{{global_asset('assets/common/js/tabler-iconpicker.js')}}"></script>

<script>
    window.appUrl = "{{ url('/') }}";
    window.currencySymbol = {
        currencyPosition: `{{get_static_option('site_currency_symbol_position')}}`,
        symbol: `{{site_currency_symbol()}}`
    };

    window.SwalTranslations = {
        deleteTitle: '{{__("Are you sure?")}}',
        deleteText: '{{__("You would not be able to revert this item!")}}',
        confirmBtn: "{{__('Yes, Accept it!')}}",
        cancelBtn: "{{__('Cancel')}}"
    };

    toastr.options = {
        "closeButton": true,
        "newestOnTop": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "slideUp",
        "rtl": "{{get_user_lang_direction() == 1}}"
    }

    const translatedDataTable = () => {
        return {
            "decimal": "",
            "emptyTable": "{{__('No data available in table')}}",
            "info": "{{__('Showing')}} _START_ {{__('to')}} _END_ {{__('of')}} _TOTAL_ {{__('entries')}}",
            "infoEmpty": "{{__('Showing')}} 0 {{__('to')}} 0 {{__('of')}} 0 {{__('entries')}}",
            "infoFiltered": "({{__('filtered from')}} _MAX_ {{__('total entries')}})",
            "infoPostFix": "",
            "thousands": ",",
            "lengthMenu": "{{__('Show')}} _MENU_ {{__('entries')}}",
            "loadingRecords": "{{__('Loading...')}}",
            "processing": "",
            "search": "{{__('Search:')}}",
            "zeroRecords": "{{__('No matching records found')}}",
            "paginate": {
                "first": "{{__('First')}}",
                "last": "{{__('Last')}}",
                "next": "{{__('Next')}}",
                "previous": "{{__('Previous')}}"
            },
            "aria": {
                "sortAscending": ": activate to sort column ascending",
                "sortDescending": ": activate to sort column descending"
            }
        }
    }

    (function($){
        "use strict";

        $(document).ready(function ($) {
            sidebar();

            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })

            $('select.select2').select2();
            $(document).on('click','.swal_delete_button',function(e){
                e.preventDefault();
                Swal.fire({
                    title: '{{__("Are you sure?")}}',
                    text: '{{__("You would not be able to revert this item!")}}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#1F51FF',
                    cancelButtonColor: '#D2042D',
                    confirmButtonText: "{{__('Yes, delete it!')}}",
                    cancelButtonText: "{{__('Cancel')}}",
                }).then((result) => {
                    if (result.isConfirmed) {
                        $(this).next().find('.swal_form_submit_btn').trigger('click');
                    }
                });
            });

            $(document).on('click','.swal_change_language_button',function(e){
                e.preventDefault();
                Swal.fire({
                    title: '{{__("Are you sure to make this language as a default language?")}}',
                    text: '{{__("Languages will be turn changed as default")}}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#1F51FF',
                    cancelButtonColor: '#D2042D',
                    confirmButtonText: "{{__('Yes, Change it!')}}",
                    cancelButtonText: "{{__('Cancel')}}",
                }).then((result) => {
                    if (result.isConfirmed) {
                        $(this).next().find('.swal_form_submit_btn').trigger('click');
                    }
                });
            });

            $(document).on('click','.swal_change_approve_payment_button',function(e){
                e.preventDefault();
                Swal.fire({
                    title: '{{__("Are you sure to approve this payment?")}}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#1F51FF',
                    cancelButtonColor: '#D2042D',
                    confirmButtonText: "{{__('Yes, Accept it!')}}",
                    cancelButtonText: "{{__('Cancel')}}",
                }).then((result) => {
                    if (result.isConfirmed) {
                        $(this).next().find('.swal_form_submit_btn').trigger('click');
                    }
                });
            });

            $(document).on('click','.swal_status_change',function(e){
                e.preventDefault();
                Swal.fire({
                    title: '{{__("Are you sure to change this status?")}}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#1F51FF',
                    cancelButtonColor: '#D2042D',
                    confirmButtonText: "{{__('Yes, Change it!')}}",
                    cancelButtonText: "{{__('Cancel')}}",
                }).then((result) => {
                    if (result.isConfirmed) {
                        $(this).next().find('.swal_form_submit_btn').trigger('click');
                    }
                });
            });

            $(document).on('click', '.submenu-disabled', function (e){
                e.preventDefault();

                let text = $(this).text();

                Swal.fire({
                    title: '{{__("Coming Soon")}}'+'\n'+text,
                    text: '{{__("This feature is currently under development and will be available soon. We are committed to continuously enhancing our application to provide you with the best possible user experience.")}}',
                    icon: 'info',
                    showCancelButton: false,
                    confirmButtonColor: '#3fc3ee',
                    confirmButtonText: "{{__('Got it')}}",
                });
            });
        });
    })(jQuery);

    function sidebar()
    {
        let tax_system = `{{get_static_option('tax_system') ?? 'zone_wise_tax_system'}}`;
        let tax_manage_menu = $('#tax-manage-menu-items');
        let country_state = tax_manage_menu.find('ul').children().slice(0,2);
        let tax_class = tax_manage_menu.find('ul').children().slice(3,4);
        if(tax_system === 'zone_wise_tax_system'){
            country_state.fadeIn();
            tax_class.fadeOut();
        } else {
            country_state.fadeOut();
            tax_class.fadeIn();
        }
    }
</script>

<script>
    $(".date").flatpickr({
        enableTime: true,
        minDate: "today",
        time_12hr: true,
        altInput: true,
        defaultDate: "2018-04-24 16:57"
    });
</script>

<script src="{{global_asset('assets/new-landlord/admin/js/ui-helpers.js')}}"></script>
<script src="{{global_asset('assets/new-landlord/admin/js/dropdown.js')}}"></script>
<script src="{{global_asset('assets/new-landlord/admin/js/app.js')}}"></script>


@yield('scripts')
@pluginAdminScripts

{{-- RTL: override toggleRowMenu to position from the left edge in RTL mode --}}
@if(\App\Facades\GlobalLanguage::custom_default_dir() === 'rtl')
<script>
(function () {
    window.toggleRowMenu = function (btn) {
        var menu = btn.nextElementSibling;
        var isHidden = menu.classList.contains('hidden');

        document.querySelectorAll('.row-action-menu').forEach(function (m) {
            m.classList.add('hidden');
            m.style.maxHeight = '';
        });

        if (isHidden) {
            var rect       = btn.getBoundingClientRect();
            var spaceBelow = window.innerHeight - rect.bottom - 8;
            var spaceAbove = rect.top - 8;

            // RTL: align the left edge of the menu with the left edge of the button
            menu.style.left  = rect.left + 'px';
            menu.style.right = 'auto';

            if (spaceBelow >= spaceAbove || spaceBelow >= 150) {
                menu.style.top    = (rect.bottom + 4) + 'px';
                menu.style.bottom = 'auto';
                menu.style.maxHeight = Math.max(150, spaceBelow) + 'px';
            } else {
                menu.style.bottom = (window.innerHeight - rect.top + 4) + 'px';
                menu.style.top    = 'auto';
                menu.style.maxHeight = Math.max(150, spaceAbove) + 'px';
            }

            menu.classList.remove('hidden');
        }
    };
})();
</script>
@endif

<!-- footer for PWA -->
@yield('pwa-footer')
</body>
</html>
