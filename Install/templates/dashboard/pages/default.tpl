<div zender-wrapper>
    <div class="page-title">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="float-left">
                        <h1>
                            <i class="la la-chart-bar la-lg"></i>
                            <span class="d-none d-sm-inline">{lang_dashboard_default_title}</span>
                        </h1>
                    </div>

                    <div class="float-right">
                        <button class="btn btn-lg btn-primary" zender-toggle="zender.sms.quick">
                            <i class="la la-telegram la-lg"></i>
                            <span class="d-none d-sm-inline">{lang_dashboard_btn_smsquick}</span>
                        </button>

                        <button class="btn btn-lg btn-primary" zender-toggle="zender.add.contact">
                            <i class="la la-address-book la-lg"></i>
                            <span class="d-none d-sm-inline">{lang_dashboard_btn_addcontact}</span>
                        </button>

                        <button class="btn btn-lg btn-primary" zender-toggle="zender.add.device">
                            <i class="la la-android la-lg"></i>
                            <span class="d-none d-sm-inline">{lang_dashboard_btn_adddevice}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-lg-9">
                <div class="card animated fadeIn">
                    <div class="card-header border-0">
                        <h2>
                            <i class="la la-chart-line"></i> 
                            {lang_dashboard_default_summarytitle}
                        </h2>
                        <h4 class="text-success">{lang_dashboard_default_summarysubtitle}</h4>
                    </div>

                    <div class="card-body pt-0">
                        <div class="embed-responsive">
                            <iframe class="embed-responsive-item position-relative" zender-iframe="{site_url}/widget/chart/dashboard.default"></iframe>
                        </div>

                        <div class="text-center">
                            <div class="row">
                                <div class="col-xl-3 col-6">
                                    <h4 class="mb-1 text-uppercase">
                                        <i class="la la-telegram la-lg"></i> {lang_dashboard_default_summarysent}
                                    </h4>
                                    <h4>{$data.total.sent}</h4>
                                </div>
                                
                                <div class="col-xl-3 col-6">
                                    <h4 class="mb-1 text-uppercase">
                                        <i class="la la-sms la-lg"></i> {lang_dashboard_default_summaryreceived}
                                    </h4>
                                    <h4>{$data.total.received}</h4>
                                </div>

                                <div class="col-xl-3 col-6">
                                    <h4 class="mb-1 text-uppercase">
                                        <i class="la la-key la-lg"></i> {lang_dashboard_default_summarykeys}
                                    </h4>
                                    <h4>{$data.subscription.used.keys}</h4>
                                </div>

                                <div class="col-xl-3 col-6">
                                    <h4 class="mb-1 text-uppercase">
                                        <i class="la la-code-branch la-lg"></i> {lang_dashboard_default_summaryhooks}
                                    </h4>
                                    <h4>{$data.subscription.used.webhooks}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {_block("1679091c5a880faf6fb5e6087eb1b2dc")}

                <div class="row">
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header border-0 pb-0">
                                <h4 class="card-title">
                                    <i class="la la-telegram"></i> {lang_dashboard_default_lastsent}
                                </h4>
                                <a href="{site_url("dashboard/messages")}" zender-nav>
                                    {lang_dashboard_default_viewall}
                                </a>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table mb-0">
                                        <tbody class="text-center">
                                            {if !empty($data.recent.sent)}
                                                {foreach $data.recent.sent as $sent}
                                                <tr>
                                                    <td>
                                                        {$sent.name}<br>
                                                        {$sent.phone}
                                                    </td>
                                                    <td>
                                                        {$sent.device}<br>
                                                        {$sent.message}
                                                    </td>
                                                    <td>{$sent.create_date}</td>
                                                </tr>
                                                {/foreach}
                                            {else}
                                            <tr>
                                                <td class="text-center">
                                                    {lang_dashboard_default_nothinghere}
                                                </td>
                                            </tr>
                                            {/if}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header border-0 pb-0">
                                <h4 class="card-title">
                                    <i class="la la-sms"></i> {lang_dashboard_default_lastreceived}
                                </h4>
                                <a href="{site_url("dashboard/messages")}" zender-nav>
                                    {lang_dashboard_default_viewall}
                                </a>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table mb-0">
                                        <tbody class="text-center">
                                            {if !empty($data.recent.received)}
                                                {foreach $data.recent.received as $sent}
                                                <tr>
                                                    <td>
                                                        {$sent.name}<br>
                                                        {$sent.phone}
                                                    </td>
                                                    <td>
                                                        {$sent.device}<br>
                                                        {$sent.message}
                                                    </td>
                                                    <td>{$sent.receive_date}</td>
                                                </tr>
                                                {/foreach}
                                            {else}
                                            <tr>
                                                <td class="text-center">
                                                    {lang_dashboard_default_nothinghere}
                                                </td>
                                            </tr>
                                            {/if}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="card subscription-widget">
                    <div class="card-body">
                        <div class="text-center">
                            <h3 class="text-uppercase">
                                <i class="la la-crown"></i> {lang_dashboard_default_packagetitle}
                            </h3>
                        </div>
                        <ul class="list-unstyled">
                            <li class="media">
                                <i class="la la-telegram mr-2"></i>
                                <div class="media-body">
                                    <h5 class="m-0 text-uppercase">
                                        {lang_dashboard_default_packagesend}
                                        <span class="help" zender-toggle="zender.view/tooltips-1">
                                            <i class="la la-question-circle"></i>
                                        </span>
                                    </h5>
                                </div>
                                <div class="text-right">
                                    <span class="text-warning">{$data.subscription.used.messages.sent}</span>
                                    <h5>{$data.subscription.package.send}</h5>
                                </div>
                            </li>
                            <li class="media">
                                <i class="la la-sms mr-2"></i>
                                <div class="media-body">
                                    <h5 class="m-0 text-uppercase">
                                        {lang_dashboard_default_packagereceive}
                                        <span class="help" zender-toggle="zender.view/tooltips-2">
                                            <i class="la la-question-circle"></i>
                                        </span>
                                    </h5>
                                </div>
                                <div class="text-right">
                                    <span class="text-warning">{$data.subscription.used.messages.received}</span>
                                    <h5>{$data.subscription.package.receive}</h5>
                                </div>
                            </li>
                            <li class="media">
                                <i class="la la-android mr-2"></i>
                                <div class="media-body">
                                    <h5 class="m-0 text-uppercase">
                                        {lang_dashboard_default_packagedevice}
                                        <span class="help" zender-toggle="zender.view/tooltips-3">
                                            <i class="la la-question-circle"></i>
                                        </span>
                                    </h5>
                                </div>
                                <div class="text-right">
                                    <span class="text-warning">{$data.subscription.used.devices}</span>
                                    <h5>{$data.subscription.package.device}</h5>
                                </div>
                            </li>
                            <li class="media">
                                <i class="la la-cube mr-2"></i>
                                <div class="media-body">
                                    <h5 class="m-0 text-uppercase">
                                        {lang_dashboard_default_package}
                                        <span class="help" zender-toggle="zender.view/tooltips-4">
                                            <i class="la la-question-circle"></i>
                                        </span>
                                    </h5>
                                </div>
                                <div class="text-right">
                                    <span class="text-warning">{$data.subscription.package.expire_date}</span>
                                    <h5>{$data.subscription.package.name}</h5>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

                {_block("e4da3b7fbbce2345d7772b0674a318d5")}
            </div>
        </div>
    </div>
</div>