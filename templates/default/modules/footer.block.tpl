<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 mb-2">
                {logo("landing", "
                    <div class=\"logo\">
                        <i class=\"la la-telegram\"></i>
                        <span>{system_site_name}</span>
                    </div>
                ")}
            </div>

            <div class="col-lg-3 col-6 mb-2">
                <h4 class="menu-items text-uppercase">{lang_landing_footer_ourcomp}</h4>
                <ul class="menu-items">
                    <li>
                        <a href="#" smspilot-toggle="eccbc87e4b5ce2fe28308fd9f2a7baf3">{lang_landing_footer_about}</a>
                    </li>
                    <li>
                        <a href="#" smspilot-toggle="c81e728d9d4c2f636f067f89cc14862c">{lang_landing_footer_privacy}</a>
                    </li>
                    <li>
                        <a href="#" smspilot-toggle="c4ca4238a0b923820dcc509a6f75849b">{lang_landing_footer_tos}</a>
                    </li>
                </ul>
            </div>

            <div class="col-lg-3 col-6 mb-2">
                <h4 class="menu-items text-uppercase">{lang_landing_footer_links}</h4>
                <ul class="menu-items">
                    <li>
                        <a href="#" smspilot-toggle="c20ad4d76fe97759aa27a0c99bff6710">{lang_landing_footer_contact}</a>
                    </li>
                    <li>
                        <a href="#" smspilot-toggle="smspilot.login">{lang_landing_footer_login}</a>
                    </li>
                    {if system_registrations < 2}
                    <li>
                        <a href="#" smspilot-toggle="smspilot.register">{lang_landing_footer_register}</a>
                    </li>
                    {/if}
                </ul>
            </div>

            <div class="col-lg-3 mb-2">
                <div class="mb-3">
                    <h4 class="menu-items text-uppercase">{lang_landing_footer_startfree}</h4>
                    <p class="footer-text">
                        {lang_landing_footer_startdesc}
                    </p>
                </div>
            </div>
        </div>

        <p class="copyright">
            {lang_landing_footer_copyright} &copy; {date("Y")}
        </p>
    </div>
</footer>