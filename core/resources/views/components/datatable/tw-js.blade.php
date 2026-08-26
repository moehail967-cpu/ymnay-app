<!-- DataTables JS – Tailwind UI version -->
<script src="{{ global_asset('assets/common/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ global_asset('assets/common/js/dataTables.responsive.min.js') }}"></script>
<script>
(function ($) {
    "use strict";
    $(document).ready(function () {
        $('.tw-table-wrap > table').DataTable({
            "order": [[0, "desc"]],
            "pageLength": 10,
            "deferRender": true,
            "processing": true,
            'columnDefs': [{
                'targets': 'no-sort',
                "orderable": false
            }],
            'language': translatedDataTable()
        });
    });
})(jQuery);
</script>
