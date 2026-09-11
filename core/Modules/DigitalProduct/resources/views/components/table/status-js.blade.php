<script>
    function toggleStatusMenu(btn) {
        var menu = btn.nextElementSibling;
        document.querySelectorAll('.product-status-dropdown .status-menu.show').forEach(function(m) {
            if (m !== menu) m.classList.remove('show');
        });
        menu.classList.toggle('show');
    }

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.product-status-dropdown')) {
            document.querySelectorAll('.product-status-dropdown .status-menu.show').forEach(function(m) {
                m.classList.remove('show');
            });
        }
    });

    $(document).on("click", ".product-status-dropdown .status-option", function () {
        var $btn = $(this);
        var id = $btn.data("id");
        var statusId = $btn.data("status-id");
        var statusText = $btn.text().trim();
        var $trigger = $btn.closest('.product-status-dropdown').find('.status-trigger');

        var colorClass = statusText === 'Publish' ? 'bg-success-soft text-success' : 'bg-warning-soft text-warning';
        $trigger.removeClass('bg-success-soft text-success bg-warning-soft text-warning bg-info-soft text-info').addClass(colorClass);
        $trigger.contents().first()[0].textContent = statusText + ' ';

        $btn.closest('.status-menu').removeClass('show');

        $.ajax({
            url: '{{ route("tenant.admin.digital.product.update.status") }}',
            type: 'POST',
            data: { id: id, status_id: statusId, _token: '{{ csrf_token() }}' },
            success: function (data) {
                if (data.success) { toastr.success(data.msg); } else { toastr.warning(data.msg); }
            },
            error: function () { toastr.error('{{ __('Something went wrong') }}'); }
        });
    });
</script>
