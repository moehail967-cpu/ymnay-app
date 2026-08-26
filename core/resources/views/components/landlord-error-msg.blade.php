@if($errors->any())
<script>
(function waitToastr() {
    if (typeof toastr === 'undefined') { return setTimeout(waitToastr, 60); }
    toastr.options = {
        closeButton:     true,
        progressBar:     true,
        positionClass:   'toast-top-right',
        timeOut:         6000,
        extendedTimeOut: 2000,
        showDuration:    200,
        hideDuration:    400,
    };
    @foreach($errors->all() as $error)
        toastr.error({{ Js::from($error) }});
    @endforeach
})();
</script>
@endif
