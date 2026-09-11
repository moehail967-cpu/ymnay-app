<script>
    $(document).on('click', '#bulk_delete_btn', function (e) {
        e.preventDefault();

        var bulkOption = $('#bulk_option').val();
        var allIds = $('.bulk-checkbox:checked').map(function() {
            return $(this).val();
        }).get();

        if (allIds.length > 0 && bulkOption == 'delete') {
            var $btn = $(this);
            $btn.html('<i class="fas fa-spinner fa-spin mr-1"></i>{{__("Deleting")}}');

            $.ajax({
                type: "POST",
                url: "{{$url}}",
                data: {
                    _token: "{{csrf_token()}}",
                    ids: allIds
                },
                success: function (data) {
                    toastr.success(data.message || 'Brands deleted successfully');
                    setTimeout(() => location.reload(), 1000);
                },
                error: function (xhr) {
                    if (xhr.status === 409) {
                        toastr.error(xhr.responseJSON.message);
                    } else if (xhr.status === 500) {
                        toastr.error('A server error occurred. Please check if some brands are linked to products.');
                    } else {
                        toastr.error('Something went wrong. Please try again.');
                    }

                }

            });
        }
    });


    $('.all-checkbox').on('change',function (e) {
        e.preventDefault();
        var value = $('.all-checkbox').is(':checked');
        var allChek = $(this).parent().parent().parent().parent().parent().find('.bulk-checkbox');
        if( value == true){
            allChek.prop('checked',true);
        }else{
            allChek.prop('checked',false);
        }
    });
</script>
