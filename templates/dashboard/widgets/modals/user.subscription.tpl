<div class="modal-content">
    <div class="modal-header">
        <h3 class="modal-title">
            <i class="la la-crown la-lg"></i> {$title}
        </h3>

        <button type="button" class="close" data-dismiss="modal">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    
    <div class="modal-body">
        <div class="p-4">
            <div class="media">
                <img class="mr-3 rounded-circle" src="{avatar}" width="100" height="100">

                <div class="media-body pt-3">
                    <span>{lang_form_welcome}</span>
                    <h3 class="mb-1">{logged_name}</h3>
                    <p>
                        <i class="la la-envelope la-lg mr-1"></i>
                        {logged_email}
                    </p>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-6">
                    <ul class="text-left">
                        <li>
                            <h3 class="text-uppercase">
                                {lang_form_package}
                            </h3>
                            <h4 class="text-muted">{$data.subscription.package.name}</h4>
                            <h4 class="text-warning">{$data.subscription.package.expire_date}</h4>
                        </li>
                        <li class="mt-3">
                            <h3 class="text-uppercase">
                                {lang_form_send}
                            </h3>
                            <h4 class="text-muted">{$data.subscription.used.messages.sent} / {$data.subscription.package.send} {lang_form_daily}</h4>
                        </li>
                        <li class="mt-3">
                            <h3 class="text-uppercase">
                                {lang_form_receive}
                            </h3>
                            <h4 class="text-muted">{$data.subscription.used.messages.received} / {$data.subscription.package.receive} {lang_form_daily}</h4>
                        </li>
                    </ul>
                </div>

                <div class="col-6">
                    <ul class="text-right">
                        <li>
                            <h3 class="text-uppercase">
                                {lang_user_subscriptioncontacts}
                            </h3>
                            <h4 class="text-muted">{$data.subscription.used.contacts} / {$data.subscription.package.contact}</h4>
                        </li>
                        <li class="mt-3">
                            <h3 class="text-uppercase">
                                {lang_form_subdevices}
                            </h3>
                            <h4 class="text-muted">{$data.subscription.used.devices} / {$data.subscription.package.device}</h4>
                        </li>
                        <li class="mt-3">
                            <h3 class="text-uppercase">
                                {lang_form_keys}
                            </h3>
                            <h4 class="text-muted">{$data.subscription.used.keys} / {$data.subscription.package.key}</h4>
                        </li>
                        <li class="mt-3">
                            <h3 class="text-uppercase">
                                {lang_form_hooks}
                            </h3>
                            <h4 class="text-muted">{$data.subscription.used.webhooks} / {$data.subscription.package.webhook}</h4>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>