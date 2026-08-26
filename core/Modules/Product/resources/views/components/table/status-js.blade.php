<script>
    function ajax_toastr_success_message(data) {
        if (data.success) {
            toastr.success(data.msg)
        } else {
            toastr.warning(data.msg);
        }
    }

    function ajax_toastr_error_message(xhr) {
        $.each(xhr.responseJSON.errors, function (key, value) {
            toastr.error((key.capitalize()).replace("-", " ").replace("_", " "), value);
        });
    }

    /*
    ========================================
        Toggle Status Menu
    ========================================
    */
    function toggleStatusMenu(btn) {
        // Close all other open menus first
        document.querySelectorAll('.product-status-dropdown .status-menu.show').forEach(function(menu) {
            if (menu !== btn.nextElementSibling) {
                menu.classList.remove('show');
            }
        });
        // Toggle the clicked menu
        btn.nextElementSibling.classList.toggle('show');
    }

    // Close menu on outside click
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.product-status-dropdown')) {
            document.querySelectorAll('.product-status-dropdown .status-menu.show').forEach(function(menu) {
                menu.classList.remove('show');
            });
        }
    });

    /*
    ========================================
        Status Option Click Handler
    ========================================
    */
    $(document).on('click', '.product-status-dropdown .status-option', function() {
        let el = $(this);
        let id = el.data('id');
        let statusId = el.data('status-id');
        let dropdown = el.closest('.product-status-dropdown');
        let trigger = dropdown.find('.status-trigger');
        let statusText = el.text().trim();

        // Update trigger button text
        trigger.find('.status-text').text(statusText);

        // Update trigger button classes
        trigger.removeClass('bg-success-soft text-success bg-warning-soft text-warning bg-info-soft text-info');
        let icon = trigger.find('i').first();
        icon.removeClass('mdi-check-circle mdi-pencil-outline mdi-clock-outline');

        if (statusId == 1) {
            trigger.addClass('bg-success-soft text-success');
            icon.addClass('mdi-check-circle');
        } else if (statusId == 2) {
            trigger.addClass('bg-warning-soft text-warning');
            icon.addClass('mdi-pencil-outline');
        } else {
            trigger.addClass('bg-info-soft text-info');
            icon.addClass('mdi-clock-outline');
        }

        // Close the menu
        dropdown.find('.status-menu').removeClass('show');

        // Send AJAX request
        let data = new FormData();
        data.append("id", id);
        data.append("status_id", statusId);
        data.append("_token", "{{ csrf_token() }}");

        $.ajax({
            url: '{{ route('tenant.admin.product.update.status') }}',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}",
            },
            processData: false,
            contentType: false,
            data: data,
            beforeSend: function() {
                toastr.info("Updating status...");
            },
            success: function(data) {
                ajax_toastr_success_message(data);
            },
            error: function(xhr) {
                ajax_toastr_error_message(xhr);
            }
        });
    });
</script>
