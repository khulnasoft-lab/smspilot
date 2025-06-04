<header class="header" zender-navbar>
    <div class="container">
        <nav class="navbar">
            <div class="container navbar-wrap">
                <div class="navbar-left">
                    <a href="{site_url}" zender-nav>{logo("landing", "
                        <div class=\"top-logo\">
                            <i class=\"la la-telegram\"></i>
                            <span class=\"d-none d-sm-inline\">
                                {system_site_name}
                            </span>
                        </div>
                    ")}</a>
                </div>
                
                <div class="navbar-right">
                    <div class="mobile-menu">
                        <ul>
                            <li>
                                <a href="#features" zender-scroll>
                                    <i class="la la-tools la-lg"></i> {lang_landing_nav_features}
                                </a>
                            </li>
                            <li>
                                <a href="#pricing" zender-scroll>
                                    <i class="la la-coins la-lg"></i> {lang_landing_nav_pricing}
                                </a>
                            </li>
                            <li>
                                <a href="#clients" zender-scroll>
                                    <i class="la la-user-friends la-lg"></i> {lang_landing_nav_clients}
                                </a>
                            </li>
                            <li>
                                <a href="#" zender-toggle="zender.login">
                                    <i class="la la-user-circle la-lg"></i> {lang_landing_nav_login}
                                </a>
                            </li>
                        </ul>
                    </div>

                    <ul class="top-menu">
                        <li>
                            <a href="#features" zender-scroll>
                                <i class="la la-tools la-lg"></i> {lang_landing_nav_features}
                            </a>
                        </li>
                        <li>
                            <a href="#pricing" zender-scroll>
                                <i class="la la-coins la-lg"></i> {lang_landing_nav_pricing}
                            </a>
                        </li>
                        <li>
                            <a href="#clients" zender-scroll>
                                <i class="la la-user-friends la-lg"></i> {lang_landing_nav_clients}
                            </a>
                        </li>
                        <li>
                            <a href="#" zender-toggle="zender.api">
                                <i class="la la-terminal la-lg"></i> {lang_landing_nav_api}
                            </a>
                        </li>
                        <li>
                            <a href="#" class="btn btn-primary btn-lg text-uppercase" zender-toggle="zender.login">
                                <i class="la la-user-circle la-lg"></i> {lang_landing_nav_login}
                            </a>
                        </li>
                    </ul>

                    <div class="flex">
                        <div class="menu-toggle-icon">
                            <div class="menu-toggle">
                                <div class="menu">
                                    <input type="checkbox" />
                                    <div></div>
                                    <div></div>
                                    <div></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <div class="container">
            <div class="row header-wrap align-items-center justify-content-center">
                <div class="col-12">
                    <div class="header-wrap">
                        {if $page eq "pages"}
                        <h2 class="header-title">
                            {$data.page.name}
                        </h2>
                        {else}
                        <h2 class="header-title">
                            {lang_landing_lead_head}
                        </h2>

                        <p class="header-description">
                            {lang_landing_lead_desc}
                        </p>

                        {if system_registrations < 2}
                        <div class="flex start center header-btns">
                            <button class="btn btn-primary btn-lg text-uppercase mr-3 mb-1 mr-0-sm" zender-toggle="zender.register">
                                <i class="la la-edit la-lg"></i> {lang_landing_lead_btn}
                            </button>
                        </div>
                        {/if}
                        {/if}
                    </div>

                    <img class="header-screen" src="{assets("images/landing.png", "default")}">
                </div>
            </div>
        </div>
    </div>
</header>