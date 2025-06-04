<div class="container" smspilot-wrapper>
    <div class="page-title">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="float-left">
                        <h1>
                            <i class="la la-sms la-lg"></i>
                            <span class="d-none d-sm-inline">{lang_dashboard_messages_title}</span>
                        </h1>
                    </div>
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
                                    <a href="#" class="nav-link active" smspilot-tab="smspilot.{$page}.sent" smspilot-tab-default>
                                        <i class="la la-telegram"></i>
                                        <span>{lang_dashboard_messages_menusent}</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link" smspilot-tab="smspilot.{$page}.received">
                                        <i class="la la-sms"></i>
                                        <span>{lang_dashboard_messages_menureceived}</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link" smspilot-tab="smspilot.{$page}.scheduled">
                                        <i class="la la-clock"></i>
                                        <span>{lang_dashboard_messages_menuscheduled}</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link" smspilot-tab="smspilot.{$page}.templates">
                                        <i class="la la-wrench"></i>
                                        <span>{lang_dashboard_messages_menutemplates}</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                {_block("c51ce410c124a10e0db5e4b97fc2af39")}
            </div>

            <div class="col-xl-9 col-md-8">
                <smspilot-tab-content></smspilot-tab-content>

                {_block("1679091c5a880faf6fb5e6087eb1b2dc")}
            </div>
        </div>
    </div>
</div>