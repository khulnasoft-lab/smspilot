(function($) {
    "use strict";
    $(function() {
        /**
         * Initial Load
         */

        alert.setup();
        zender.select();

        $("[zender-form]").on("submit", function(e) {
            e.preventDefault();

            var required = "site_name|Site name<=>site_desc|Site description<=>protocol|Protocol<=>dbhost|Database host<=>dbname|Database name<=>dbuser|Database username<=>name|Full name<=>email|Email address<=>password|Password";
            var data = new FormData(this);

            $.ajax({
                url: "./install/ajax",
                type: "POST",
                data: data,
                contentType: false,
                processData: false,
                beforeSend: () => {
                    var filter = required.split("<=>");
                    for (var i = 0; i <= filter.length; i++) {
                        if (typeof filter[i] !== "undefined") {
                            var values = filter[i].split("|");
                        }
                        try {
                            if (data.get(values[0]).length < 1) {
                                alert.warning(values[1] + ", " + lang_validate_cannotemp);
                                return false;
                            }
                        } catch (e) {
                            if (data.getAll(values[0] + "[]").length < 1) {
                                alert.warning(values[1] + ", " + lang_validate_cannotemp);
                                return false;
                            }
                        }
                    }

                    zender.disabled();
                },
                success: (response) => {
                    try {
                        var response = JSON.parse(response);

                        switch (response.status) {
                            case 200:
                                $("[zender-install]").fadeOut("fast", function() {
                                    $("[zender-installed]").fadeIn("fast");
                                });
                                break;
                            default:
                                alert.danger(response.message);
                        }
                    } catch (e) {
                        alert.danger(lang_response_went_wrong);
                    }

                    zender.disabled(false);
                }
            });
        });

        /**
         * Preloader
         */

        $("[zender-preloader]").fadeOut("fast", () => {
            zender.ripple();
        });
    });
})(jQuery);