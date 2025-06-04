    {include "./modules/footer.block.tpl"}

    <div zender-preloader>
        <div class="sk-three-bounce">
            <div class="sk-child sk-bounce1"></div>
            <div class="sk-child sk-bounce2"></div>
            <div class="sk-child sk-bounce3"></div>
        </div>
    </div>

    <script src="{_assets("js/libs/fetch.min.js")}"></script>
    <script>
        window.template = "{template}";
        window.site_url = "{site_url}";
        window.stripe_key = "{system_stripe_key}";

        var lang_datatable_processing = "{lang_datatable_processing}",
            lang_datatable_length = "{lang_datatable_length}",
            lang_datatable_info = "{lang_datatable_info}",
            lang_datatable_empty = "{lang_datatable_empty}",
            lang_datatable_filtered = "{lang_datatable_filtered}",
            lang_datatable_loading = "{lang_datatable_loading}",
            lang_datatable_zero = "{lang_datatable_zero}",
            lang_datatable_null = "{lang_datatable_null}",
            lang_datatable_first = "{lang_datatable_first}",
            lang_datatable_prev = "{lang_datatable_prev}",
            lang_datatable_next = "{lang_datatable_next}",
            lang_datatable_last = "{lang_datatable_last}",
            lang_response_went_wrong = "{lang_response_went_wrong}",
            lang_delete_title = "{lang_delete_title}",
            lang_delete_tagline = "{lang_delete_tagline}",
            lang_validate_cannotemp = "{lang_validate_cannotemp}",
            lang_alert_attention = "{lang_alert_attention}",
            lang_date_today = "{lang_date_today}",
            lang_date_yesterday = "{lang_date_yesterday}",
            lang_date_7days = "{lang_date_7days}",
            lang_date_30days = "{lang_date_30days}",
            lang_date_month = "{lang_date_month}",
            lang_date_lmonth = "{lang_date_lmonth}",
            lang_date_custom = "{lang_date_custom}",
            lang_copy_data = "{lang_copy_data}",
            lang_unknown_action_method = "{lang_unknown_action_method}",
            lang_btn_confirm = "{lang_btn_confirm}",
            lang_btn_cancel = "{lang_btn_cancel}",
            lang_suspend_user_title = "{lang_suspend_user_title}",
            lang_suspend_user_desc = "{lang_suspend_user_desc}",
            lang_unsuspend_user_title = "{lang_unsuspend_user_title}",
            lang_unsuspend_user_desc = "{lang_unsuspend_user_desc}",
            lang_alert_impersonate_title = "{lang_alert_impersonate_title}",
            lang_alert_impersonate_desc = "{lang_alert_impersonate_desc}",
            lang_alert_impersonateexit_title = "{lang_alert_impersonateexit_title}",
            lang_alert_impersonateexit_desc = "{lang_alert_impersonateexit_desc}";

        fetchInject([
            "{_assets("js/custom.js")}"
        ], fetchInject([
            "{assets("js/template.js")}"
        ], fetchInject([
            "https://js.stripe.com/v3"
        ], fetchInject([
            "{_assets("js/functions.js")}",
            "{_assets("js/libs/he.min.js")}",
            "{_assets("js/libs/mfb.min.js")}",
            "{_assets("js/libs/card.min.js")}"
        ], fetchInject([
            "{_assets("js/libs/pjax.min.js")}",
            "{_assets("js/libs/waves.min.js")}",
            "{_assets("js/libs/topbar.min.js")}",
            "{_assets("js/libs/moment.min.js")}",
            "{_assets("js/libs/qrcode.min.js")}",
            "{_assets("js/libs/scrollto.min.js")}",
            "{_assets("js/libs/izitoast.min.js")}",
            "{_assets("js/libs/codeflask.min.js")}",
            "{_assets("js/libs/clipboard.min.js")}",
            "{_assets("js/libs/autocomplete.min.js")}",
            "{_assets("js/libs/iframeResizer.min.js")}",
            "{_assets("js/libs/daterangepicker.min.js")}",
            "{_assets("js/libs/bootstrap-select.min.js")}",
            "{_assets("js/libs/datatables/datatables.min.js")}"
        ], fetchInject([
            "{_assets("js/libs/bootstrap.min.js")}"
        ], fetchInject([
            "{_assets("js/libs/jquery.min.js")}",
            "{_assets("css/custom.css")}",
            "{_assets("css/libs/mfb.min.css")}",
            "{_assets("css/libs/waves.min.css")}",
            "{_assets("css/libs/animate.min.css")}",
            "{_assets("css/libs/izitoast.min.css")}",
            "{_assets("css/libs/datatables.min.css")}",
            "{_assets("css/libs/daterangepicker.min.css")}",
            "{_assets("css/libs/bootstrap-select.min.css")}"
        ])))))));
    </script>
</body>

</html>