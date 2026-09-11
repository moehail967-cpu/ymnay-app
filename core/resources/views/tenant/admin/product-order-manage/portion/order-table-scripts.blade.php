<script>
(function ($) {
    "use strict";

    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }
    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    window.toggleRowMenu = function (btn) {
        var menu = btn.nextElementSibling;
        var isHidden = menu.classList.contains('hidden');
        document.querySelectorAll('.row-action-menu').forEach(function (m) { m.classList.add('hidden'); });
        if (isHidden) {
            var rect = btn.getBoundingClientRect();
            var spaceBelow = window.innerHeight - rect.bottom;
            var spaceAbove = rect.top;
            menu.style.right = (window.innerWidth - rect.right) + 'px';
            menu.style.left  = 'auto';
            if (spaceBelow >= Math.min(360, 200) || spaceBelow >= spaceAbove) {
                menu.style.top = (rect.bottom + 4) + 'px';
                menu.style.bottom = 'auto';
            } else {
                menu.style.bottom = (window.innerHeight - rect.top + 4) + 'px';
                menu.style.top = 'auto';
            }
            menu.classList.remove('hidden');
        }
    };

    document.addEventListener('click', function (e) {
        if (!e.target.closest('.row-action-wrap')) {
            document.querySelectorAll('.row-action-menu').forEach(function (m) { m.classList.add('hidden'); });
        }
    });

    window.addEventListener('scroll', function (e) {
        if (e.target && e.target.closest && e.target.closest('.row-action-menu')) return;
        document.querySelectorAll('.row-action-menu').forEach(function (m) { m.classList.add('hidden'); });
    }, true);

    document.querySelectorAll('.tw-table-wrap').forEach(function (wrap) {
        wrap.addEventListener('scroll', function () {
            document.querySelectorAll('.row-action-menu').forEach(function (m) { m.classList.add('hidden'); });
        });
    });

    $(document).ready(function () {
        $('.summernote').summernote({
            height: 250,
            codemirror: { theme: 'monokai' },
            callbacks: { onChange: function (contents) { $(this).prev('input').val(contents); } }
        });

        $(document).on('click', '.order_status_change_btn', function (e) {
            e.preventDefault();
            document.querySelectorAll('.row-action-menu').forEach(function (m) { m.classList.add('hidden'); });
            var el = $(this);
            var form = $('#order_status_change_modal');
            form.find('#order_id').val(el.data('id'));
            form.find('#order_status option[value="' + el.data('status') + '"]').attr('selected', true);

            if (el.data('payment_status') === 'success') {
                form.find('#payment_status_wrap').hide();
                form.find('#payment_status').removeAttr('name');
            } else {
                form.find('#payment_status_wrap').show();
                form.find('#payment_status').attr('name', 'payment_status');
                form.find('#payment_status option[value="' + el.data('payment_status') + '"]').attr('selected', true);
            }
            openModal('order_status_change_modal');
        });
        $('.order_status_close, #order_status_backdrop').on('click', function () { closeModal('order_status_change_modal'); });

        $(document).on('click', '.user_edit_btn', function (e) {
            e.preventDefault();
            document.querySelectorAll('.row-action-menu').forEach(function (m) { m.classList.add('hidden'); });
            openModal('send_mail_modal');
        });
        $('.send_mail_close, #send_mail_backdrop').on('click', function () { closeModal('send_mail_modal'); });

        $(document).on('click', '.swal_delete_button', function (e) {
            e.preventDefault();
            document.querySelectorAll('.row-action-menu').forEach(function (m) { m.classList.add('hidden'); });
            var btn = $(this);
            Swal.fire({
                title: '{{ __("Are you sure?") }}',
                text: '{{ __("You will not be able to recover this!") }}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#989898',
                confirmButtonText: '{{ __("Yes, Delete it!") }}',
                cancelButtonText: '{{ __("Cancel") }}',
            }).then(function (result) {
                if (result.isConfirmed) { btn.closest('td').find('.delete-form').trigger('submit'); }
            });
        });
    });
})(jQuery);
</script>
