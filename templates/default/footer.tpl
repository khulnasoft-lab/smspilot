	{include "./modules/footer.block.tpl"}

    <div smspilot-preloader>
        <div class="sk-three-bounce">
            <div class="sk-child sk-bounce1"></div>
            <div class="sk-child sk-bounce2"></div>
            <div class="sk-child sk-bounce3"></div>
        </div>
    </div>

    <script src="{_assets("js/libs/fetch.min.js")}"></script>
    <script>
        window.site_url = "{site_url}";
        window.template = "{template}";
        
        var lang_response_went_wrong = "{lang_response_went_wrong}",
            lang_validate_cannotemp = "{lang_validate_cannotemp}",
            lang_alert_attention = "{lang_alert_attention}",
            lang_cookieconsent_message = "{lang_cookieconsent_message}",
            lang_cookieconsent_link = "{lang_cookieconsent_link}";

        fetchInject([
            "{_assets("js/custom.js")}"
        ], fetchInject([
            "{assets("js/template.js")}"
        ], fetchInject([
            "{_assets("js/functions.js")}",
            "{_assets("js/libs/mfb.min.js")}"
        ], fetchInject([
            "{_assets("js/libs/pjax.min.js")}",
            "{_assets("js/libs/aos.min.js")}",
            "{_assets("js/libs/waves.min.js")}",
            "{_assets("js/libs/topbar.min.js")}",
            "{_assets("js/libs/scrollto.min.js")}",
            "{_assets("js/libs/izitoast.min.js")}",
            "{_assets("js/libs/cookieconsent.min.js")}",
            "{_assets("js/libs/iframeResizer.min.js")}"
        ], fetchInject([
            "{_assets("js/libs/bootstrap.min.js")}"
        ], fetchInject([
            "{_assets("js/libs/jquery.min.js")}",
            "{_assets("css/libs/mfb.min.css")}",
            "{_assets("css/libs/aos.min.css")}",
            "{_assets("css/libs/waves.min.css")}",
            "{_assets("css/libs/izitoast.min.css")}",
            "{_assets("css/libs/cookieconsent.min.css")}",
        ]))))));
    </script>
</body>

</html>