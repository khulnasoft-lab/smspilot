<div class="container" smspilot-wrapper>
    <div class="page-title">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="float-left">
                        <h1>
                            <i class="la la-tools la-lg"></i>
                            <span class="d-none d-sm-inline">{lang_dashboard_admin_title}</span>
                        </h1>
                    </div>

                    {if super_admin}
                    <div class="float-right">
                        <button class="btn btn-lg btn-primary" smspilot-forums>
                            <i class="la la-comments la-lg"></i>
                            <span class="d-none d-sm-inline">{lang_dashboard_btn_forums}</span>
                        </button>

                        <button class="btn btn-lg btn-primary" smspilot-toggle="smspilot.admin.theme">
                            <i class="la la-palette la-lg"></i>
                            <span class="d-none d-sm-inline">{lang_dashboard_btn_theme}</span>
                        </button>

                        <button class="btn btn-lg btn-primary" smspilot-toggle="smspilot.admin.settings">
                            <i class="la la-cog la-lg"></i>
                            <span class="d-none d-sm-inline">{lang_dashboard_btn_settings}</span>
                        </button>
                    </div>
                    {/if}
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-xl-3 col-md-4">
                <div class="tabs-menu">
                    <div class="card">
                        <div class="card-body">
                            <ul>
                                <li class="nav-item">
                                    <a href="#" class="nav-link active" smspilot-tab="smspilot.{$page}.default" smspilot-tab-default>
                                        <i class="la la-chart-area"></i>
                                        <span>{lang_dashboard_admin_menustats}</span>
                                    </a>
                                </li>
                                {if permission("manage_users")}
                                <li class="nav-item">
                                    <a href="#" class="nav-link" smspilot-tab="smspilot.{$page}.users">
                                        <i class="la la-users"></i>
                                        <span>{lang_dashboard_admin_menuusers}</span>
                                    </a>
                                </li>
                                {/if}
                                {if super_admin}
                                <li class="nav-item">
                                    <a href="#" class="nav-link" smspilot-tab="smspilot.{$page}.roles">
                                        <i class="la la-shield"></i>
                                        <span>{lang_dashboard_admin_menuroles}</span>
                                    </a>
                                </li>
                                {/if}
                                {if permission("manage_packages")}
                                <li class="nav-item">
                                    <a href="#" class="nav-link" smspilot-tab="smspilot.{$page}.packages">
                                        <i class="la la-cubes"></i>
                                        <span>{lang_dashboard_admin_menupackages}</span>
                                    </a>
                                </li>
                                {/if}
                                {if permission("manage_vouchers")}
                                <li class="nav-item">
                                    <a href="#" class="nav-link" smspilot-tab="smspilot.{$page}.vouchers">
                                        <i class="la la-money-bill-wave"></i>
                                        <span>{lang_dashboard_admin_menuvouchers}</span>
                                    </a>
                                </li>
                                {/if}
                                {if permission("manage_subscriptions")}
                                <li class="nav-item">
                                    <a href="#" class="nav-link" smspilot-tab="smspilot.{$page}.subscriptions">
                                        <i class="la la-crown"></i>
                                        <span>{lang_dashboard_admin_menusubscriptions}</span>
                                    </a>
                                </li>
                                {/if}
                                {if permission("manage_transactions")}
                                <li class="nav-item">
                                    <a href="#" class="nav-link" smspilot-tab="smspilot.{$page}.transactions">
                                        <i class="la la-coins"></i>
                                        <span>{lang_dashboard_admin_menutransactions}</span>
                                    </a>
                                </li>
                                {/if}
                                {if permission("manage_widgets")}
                                <li class="nav-item">
                                    <a href="#" class="nav-link" smspilot-tab="smspilot.{$page}.widgets">
                                        <i class="la la-puzzle-piece"></i>
                                        <span>{lang_dashboard_admin_menuwidgets}</span>
                                    </a>
                                </li>
                                {/if}
                                {if permission("manage_pages")}
                                <li class="nav-item">
                                    <a href="#" class="nav-link" smspilot-tab="smspilot.{$page}.pages">
                                        <i class="la la-stream"></i>
                                        <span>{lang_dashboard_admin_menupages}</span>
                                    </a>
                                </li>
                                {/if}
                                {if permission("manage_languages")}
                                <li class="nav-item">
                                    <a href="#" class="nav-link" smspilot-tab="smspilot.{$page}.languages">
                                        <i class="la la-language"></i>
                                        <span>{lang_dashboard_admin_menulanguages}</span>
                                    </a>
                                </li>
                                {/if}
                            </ul>
                        </div>
                    </div>
                </div>

                {if super_admin}
                <div class="card text-center">
                    <div class="card-header d-block pt-4 pb-3">
                        <h3 class="text-uppercase">
                            <i class="la la-android la-lg"></i> {lang_admin_gateway_title}
                        </h3>
                    </div>

                    <div class="card-body">
                        <h4 class="text-uppercase">{lang_admin_gateway_status}: {if $data.gateway}<span class="badge badge-success">{lang_admin_gateway_uploaded}</span>{else}<span class="badge badge-danger">{lang_admin_gateway_notuploaded}</span>{/if}</h4>
                    </div>

                    <div class="card-footer">
                        <button class="btn btn-lg btn-primary" smspilot-build>
                            <i class="la la-hammer la-lg"></i> {lang_dashboard_btn_build}
                        </button>

                        <button class="btn btn-lg btn-primary" smspilot-toggle="smspilot.admin.builder">
                            <i class="la la-tools la-lg"></i> {lang_dashboard_btn_buildsettings}
                        </button>
                    </div>
                </div>
                {/if}
            </div>

            <div class="col-xl-9 col-md-8">
                <smspilot-tab-content></smspilot-tab-content>

                {_block("1679091c5a880faf6fb5e6087eb1b2dc")}
            </div>
        </div>
    </div>
</div>