$(document).ready(function () {
    $(document).on('click', '#update-info-close-btn', function () {
        let el = $(this).closest('.floating-card-wraper');
        el.html("<i class='mdi mdi-information-variant info-icon'></i>")
        el.css("padding", "10px");
    })

    $(document).on('click', '#update-info-mark-btn', function () {
        let url = $(this).attr('data-url');

        axios.get(url)
            .then((response) => {
                $('.floating-card-wraper').hide();

                setTimeout(function () {
                    $('.floating-card-wraper').remove();
                }, 1000);
            }).catch((error) => {});
    });
});