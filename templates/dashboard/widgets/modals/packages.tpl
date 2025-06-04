<div class="modal-content">
    <div class="modal-header">
        <h3 class="modal-title">
            <i class="la la-cubes la-lg"></i> {$title}
        </h3>

        <button type="button" class="close" data-dismiss="modal">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    
    <div class="modal-body">
        <div class="row">
            {foreach $data.packages as $package}
            <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12">
                <div class="card">
                    <div class="card-header bg-primary">
                        <h2 class="card-title text-white">
                            <i class="la la-cube la-lg"></i> {$package.name}
                        </h2>
                        <small class="text-white">
                            {if $package.id < 2}
                            {lang_form_freefor}
                            {else}
                            {sign()}{$package.price} {lang_form_monthly}
                            {/if}
                        </small>
                    </div>

                    <div class="card-body">
                        <h4 class="text-uppercase">{lang_form_dailysend}</h4>
                        <h4 class="text-muted">
                            <i class="la la-telegram la-lg"></i> {$package.send}
                        </h4>

                        <h4 class="text-uppercase">{lang_form_dailyreceive}</h4>
                        <h4 class="text-muted">
                            <i class="la la-sms la-lg"></i> {$package.receive}
                        </h4>

                        <h4 class="text-uppercase">{lang_packages_dashboardallowedcontacts}</h4>
                        <h4 class="text-muted">
                            <i class="la la-address-book la-lg"></i> {$package.contacts}
                        </h4>

                        <h4 class="text-uppercase">{lang_form_alloweddevices}</h4>
                        <h4 class="text-muted">
                            <i class="la la-android la-lg"></i> {$package.devices}
                        </h4>

                        <h4 class="text-uppercase">{lang_form_allowedkeys}</h4>
                        <h4 class="text-muted">
                            <i class="la la-key la-lg"></i> {$package.keys}
                        </h4>

                        <h4 class="text-uppercase">{lang_form_allowedhooks}</h4>
                        <h4 class="text-muted">
                            <i class="la la-code-branch la-lg"></i> {$package.webhooks}
                        </h4>

                        <button class="btn btn-{if $package.id < 2}primary{else}secondary{/if} btn-lg btn-block mt-3" {if $package.id > 1}smspilot-toggle="smspilot.providers/{$package.id}"{/if} {if $package.id < 2}disabled{/if}>
                            {if $package.id < 2}
                                <i class="la la-bolt"></i> {lang_btn_free}
                            {else}
                                <i class="la la-credit-card"></i> {lang_btn_purchase}
                            {/if}
                        </button>
                    </div>
                </div>
            </div>
            {/foreach}
        </div>
    </div>
</div>