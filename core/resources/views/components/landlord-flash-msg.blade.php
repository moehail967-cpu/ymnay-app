@if(session()->has('msg'))
@php
    $type = session('type', 'success');
    $method = match($type) {
        'danger', 'error' => 'error',
        'warning'         => 'warning',
        'info'            => 'info',
        default           => 'success',
    };
@endphp
<script>
(function waitToastr() {
    if (typeof toastr === 'undefined') { return setTimeout(waitToastr, 60); }
    toastr.options = {
        closeButton:   true,
        progressBar:   true,
        positionClass: 'toast-top-right',
        timeOut:       4500,
        extendedTimeOut: 1500,
        showDuration:  200,
        hideDuration:  400,
    };
    toastr['{{ $method }}']({{ Js::from(strip_tags(session('msg'))) }});
})();
</script>
@endif
