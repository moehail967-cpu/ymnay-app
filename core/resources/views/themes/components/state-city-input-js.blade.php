<script>
    // LIVE SEARCH FOR STATE
    $(document).on("keyup", ".live-state-input", function () {
        let keyword = $(this).val();
        let state = $('#state').val();
        let dropdown = $(".state-dropdown");

        if (keyword.length < 1) {
            dropdown.hide();
            return;
        }

        $.ajax({
            url: "{{ route('tenant.shop.checkout.state.search') }}",
            data: { q: keyword , state : state},
            success: function (data) {
                dropdown.html('');

                if (data.length > 0) {
                    $.each(data, function (index, item) {
                        dropdown.append('<div data-name="' + item.name + '">' + item.name + '</div>');
                    });
                    dropdown.show();
                } else {
                    dropdown.hide();
                }
            }
        });
    });

    // Select State
    $(document).on("click", ".state-dropdown div", function () {
        let name = $(this).data("name");
        $(".live-state-input").val(name);
        $(".state-dropdown").hide();
    });


    // LIVE SEARCH FOR CITY
    $(document).on("keyup", ".live-city-input", function () {
        let keyword = $(this).val();
        let stateName = $(".live-state-input").val();
        let dropdown = $(".city-dropdown");

        if (keyword.length < 1) {
            dropdown.hide();
            return;
        }

        $.ajax({
            url: "{{ route('tenant.shop.checkout.city.search') }}",
            data: { q: keyword, state: stateName },
            success: function (data) {
                dropdown.html('');

                if (data.length > 0) {
                    $.each(data, function (index, item) {
                        dropdown.append('<div data-name="' + item.name + '">' + item.name + '</div>');
                    });
                    dropdown.show();
                } else {
                    dropdown.hide();
                }
            }
        });
    });

    // Select City
    $(document).on("click", ".city-dropdown div", function () {
        let name = $(this).data("name");
        $(".live-city-input").val(name);
        $(".city-dropdown").hide();
    });
</script>
