
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
<script src="{{asset('assets/landlord/admin/js/landlord.bundle.base.js')}}"></script>

<script src="{{asset('assets/landlord/common/js/axios.min.js')}}"></script>
<script src="{{asset('assets/landlord/common/js/sweetalert2.js')}}"></script>
<script src="{{asset('assets/common/js/flatpickr.js')}}"></script>
<x-flatpicker.flatpickr-locale />
<script src="{{asset('assets/common/js/select2.min.js')}}"></script>
<script src="{{asset('assets/common/js/toastr.min.js')}}"></script>
<script src="{{asset('assets/common/js/tabler-iconpicker.js')}}"></script>
{{--
<script src="{{asset('assets/landlord/admin/js/jquery.nice-select.min.js')}}"></script> --}}

<script>
    window.SwalTranslations = {
        deleteTitle: '{{__("Are you sure?")}}',
        deleteText: '{{__("You would not be able to revert this item!")}}',
        confirmBtn: "{{__('Yes, Accept it!')}}",
        cancelBtn: "{{__('Cancel')}}"
    };
</script>
<script src="{{asset('assets/new-landlord/admin/js/ui-helpers.js')}}"></script>
<script src="{{asset('assets/new-landlord/admin/js/dropdown.js')}}"></script>
<script src="{{asset('assets/new-landlord/admin/js/app.js')}}"></script>

@yield('scripts')
@pluginAdminScripts

{{-- RTL: override toggleRowMenu to position from the left edge in RTL mode --}}
@if(\App\Facades\GlobalLanguage::default_dir() === 'rtl')
<script>
(function () {
    var _origToggle = window.toggleRowMenu;
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
</body>

</html>
