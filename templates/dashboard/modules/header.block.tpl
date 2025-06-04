<div class="header">
    <div class="container">
        <div class="row">
            <div class="col-xl-12">
                <nav class="navbar navbar-expand-lg navbar-light px-0">
                    <a class="navbar-brand" href="{site_url("dashboard")}" zender-nav>
                        {logo("dashboard", "
                            <i class=\"la la-telegram\"></i>
                        ")}
                    </a>

                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="la la-bars"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent" zender-navbar>
                        <ul class="navbar-nav show">
                            <li class="nav-item">
                                <a class="nav-link" href="{site_url("dashboard")}" zender-nav><i class="la la-chart-bar la-lg"></i> {lang_dashboard_nav_default}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{site_url("dashboard/messages")}" zender-nav><i class="la la-sms la-lg"></i> {lang_dashboard_nav_messages}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{site_url("dashboard/contacts")}" zender-nav><i class="la la-address-book la-lg"></i> {lang_dashboard_nav_contacts}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{site_url("dashboard/devices")}" zender-nav><i class="la la-android la-lg"></i> {lang_dashboard_nav_devices}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{site_url("dashboard/tools")}" zender-nav><i class="la la-toolbox la-lg"></i> {lang_dashboard_nav_tools}</a>
                            </li>
                            {if is_admin}
                            <li class="nav-item">
                                <a class="nav-link" href="{site_url("dashboard/administration")}" zender-nav><i class="la la-tools la-lg"></i> {lang_dashboard_nav_admin}</a>
                            </li>
                            {/if}
                        </ul>
                    </div>

                    <div class="user-nav">
                        <div class="d-flex align-items-center">
                            <div class="dropdown">
                                <div class="user" data-toggle="dropdown">
                                    <span class="thumb">
                                        <img src="{avatar}" class="rounded-circle" zender-avatar>
                                    </span>
                                    <span class="name">{logged_name}</span>
                                    <span class="arrow">
                                        <i class="la la-angle-down"></i>
                                    </span>
                                </div>
                                <ul class="dropdown-menu dropdown-menu-right">
                                    <a href="#" class="dropdown-item" zender-toggle="zender.user.subscription">
                                        <i class="la la-crown"></i> {lang_dashboard_nav_menusubscription}
                                    </a>
                                    <a href="#" class="dropdown-item" zender-toggle="zender.packages">
                                        <i class="la la-cubes"></i> {lang_btn_packages}
                                    </a>
                                    <a href="#" class="dropdown-item" zender-toggle="zender.user.settings">
                                        <i class="la la-user-cog"></i> {lang_dashboard_nav_menusettings}
                                    </a>
                                    {if isset($smarty.session.impersonate)}
                                    <a href="#" class="dropdown-item" user-id="{$smarty.session.impersonate.id}" zender-action="exit">
                                        <i class="la la-sign-out"></i> {lang_impersonate_exit_header}
                                    </a>
                                    {else}
                                    <a href="#" class="dropdown-item" zender-action="logout">
                                        <i class="la la-sign-out"></i> {lang_dashboard_nav_menulogout}
                                    </a>
                                    {/if}
                                </ul>
                            </div>
                        </div>
                    </div>
                </nav>
            </div>
        </div>
    </div>
</div>