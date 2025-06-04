<div smspilot-wrapper>
    <section id="features" class="main-section bg-white">
        <div class="section-head" data-aos="fade-up">
            <h2 class="section-title">{lang_landing_feat_title}</h2>
            <p class="section-desc">
                {lang_landing_feat_titledesc}
            </p>
        </div>

        <div class="container">
            <div class="row">
                <div class="col-xs-12 flex justify-content-center">
                    <div class="feat-item">
                        <div class="container">
                            <div class="row">
                                <div class="col-lg-5 m-auto">
                                    <h4 class="feat-subtitle" data-aos="fade-down-right">
                                        {lang_landing_feat_onesub}
                                    </h4>
                                    <h3 class="feat-title" data-aos="fade-right">
                                        {lang_landing_feat_onetitle}
                                    </h3>
                                    <p class="feat-desc" data-aos="fade-right">
                                        {lang_landing_feat_onedesc}
                                    </p>
                                </div>

                                <div class="col-lg-7">
                                    <div class="feat-img" data-aos="fade-left">
                                        <img src="{assets("images/feat-1.png", "default")}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="feat-item">
                        <div class="container">
                            <div class="row">
                                <div class="col-lg-7">
                                    <div class="feat-img" data-aos="fade-right">
                                        <img src="{assets("images/feat-2.png", "default")}">
                                    </div>
                                </div>
                                <div class="col-lg-5 m-auto">
                                    <h4 class="feat-subtitle" data-aos="fade-down-left">
                                        {lang_landing_feat_twosub}
                                    </h4>
                                    <h3 class="feat-title" data-aos="fade-left">
                                        {lang_landing_feat_twotitle}
                                    </h3>
                                    <p class="feat-desc" data-aos="fade-left">
                                        {lang_landing_feat_twodesc}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="feat-item">
                        <div class="container">
                            <div class="row">
                                <div class="col-lg-5 m-auto">
                                    <h4 class="feat-subtitle" data-aos="fade-down-right">
                                        {lang_landing_feat_threesub}
                                    </h4>
                                    <h3 class="feat-title" data-aos="fade-right">
                                        {lang_landing_feat_threetitle}
                                    </h3>
                                    <p class="feat-desc" data-aos="fade-right">
                                        {lang_landing_feat_threedesc}
                                    </p>
                                </div>

                                <div class="col-lg-7">
                                    <div class="feat-img" data-aos="fade-left">
                                        <img src="{assets("images/feat-3.png", "default")}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="pricing" class="pricing bg-grey main-section">
        <div class="container">
            <div class="section-head">
                <h3 class="section-title" data-aos="fade-down">
                    {lang_landing_pricing_title}
                </h3>
                <p class="section-desc" data-aos="zoom-in">
                    {lang_landing_pricing_desc}
                </p>
            </div>

            <div class="row pricing-cards-wrap mb-0">
                {foreach $data.packages as $package}
                <div class="col-lg-4">
                    <div class="pricing-card" data-aos="flip-left" data-aos-delay="{($package@key + 1) * 500}">
                        <div class="pricing-card-title text-grad">
                            <i class="la la-cube la-lg"></i> {$package.name}
                        </div>

                        <ul class="pricing-card-options">
                            <li>
                                <i class="la la-telegram la-lg"></i> <strong>{$package.send}</strong> {lang_landing_pricing_send}
                            </li>
                            <li>
                                <i class="la la-sms la-lg"></i> <strong>{$package.receive}</strong> {lang_landing_pricing_receive}
                            </li>
                            <li>
                                <i class="la la-address-book la-lg"></i> <strong>{$package.contacts}</strong> {lang_packages_landingsavedcontacts}
                            </li>
                            <li>
                                <i class="la la-android la-lg"></i> <strong>{$package.devices}</strong> {lang_landing_pricing_devices}
                            </li>
                            <li>
                                <i class="la la-key la-lg"></i> <strong>{$package.keys}</strong> {lang_landing_pricing_keys}
                            </li>
                            <li>
                                <i class="la la-code-branch la-lg"></i> <strong>{$package.webhooks}</strong> {lang_landing_pricing_hooks}
                            </li>
                        </ul>

                        <div class="pricing-card-price">
                            {sign()}{$package.price}<span>/{lang_landing_pricing_month}</span>
                        </div>
                    </div>
                </div>
                {/foreach}
            </div>

            <div class="pricing-info text-center">
                {lang_landing_pricing_contact}
            </div>
        </div>
    </section>

    <div id="clients" class="bg-white section-main">
        <div class="container">
            <div class="section section-companies">
                {_block("8f14e45fceea167a5a36dedd4bea2543")}
            </div>
        </div>
    </div>
</div>