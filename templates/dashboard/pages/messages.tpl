<div class="container" zender-wrapper>
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
                                    <a href="#" class="nav-link active" zender-tab="zender.{$page}.sent" zender-tab-default>
                                        <i class="la la-telegram"></i>
                                        <span>{lang_dashboard_messages_menusent}</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link" zender-tab="zender.{$page}.received">
                                        <i class="la la-sms"></i>
                                        <span>{lang_dashboard_messages_menureceived}</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link" zender-tab="zender.{$page}.scheduled">
                                        <i class="la la-clock"></i>
                                        <span>{lang_dashboard_messages_menuscheduled}</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link" zender-tab="zender.{$page}.templates">
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
                <zender-tab-content></zender-tab-content>

                {_block("1679091c5a880faf6fb5e6087eb1b2dc")}
            </div>
        </div>
    </div>
</div>