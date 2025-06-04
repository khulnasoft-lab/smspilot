<form smspilot-form>
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="la la-cog la-lg"></i> {$title}
            </h3>

            <button type="button" class="close" data-dismiss="modal">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        
        <div class="modal-body">
            <div class="form-row">
                <div class="form-group mb-0 col-12">
                    <h2 class="text-uppercase">{lang_form_settingsite}</h2>
                </div>

                <div class="form-group col-3">
                    <label>{lang_form_settingssitename}</label>
                    <input type="text" name="site_name" class="form-control" placeholder="eg. Smspilot" value="{$data.system.site_name}">
                </div>

                <div class="form-group col-3">
                    <label>{lang_form_settingssitedesc}</label>
                    <input type="text" name="site_desc" class="form-control" placeholder="eg. This is an awesome sms gateway service"  value="{$data.system.site_desc}">
                </div>

                <div class="form-group col-3">
                    <label>{lang_form_settingspcode}</label>
                    <input type="text" name="purchase_code" class="form-control" placeholder="eg. e339eed64aa02808b5955f9e3a08de858b6ebfd4" value="{$data.system.purchase_code}">
                </div>

                <div class="form-group col-3">
                    <label>{lang_form_adminsettings_token}</label>
                    <input type="text" class="form-control" value="{system_token}">
                </div>

                <div class="form-group col-3">
                    <label>{lang_form_settingsprotocol}</label>
                    <select name="protocol" class="form-control">
                        <option value="1" {if $data.system.protocol < 2}selected{/if}>HTTP</option>
                        <option value="2" {if $data.system.protocol > 1}selected{/if}>HTTPS</option>  
                    </select>
                </div>

                <div class="form-group col-3">
                    <label>{lang_form_settingsdeflang}</label>
                    <select name="default_lang" class="form-control" data-live-search="true">
                        {foreach $data.languages as $language}
                        <option value="{$language@key}" data-tokens="{$language.token}" {if $data.system.default_lang eq $language@key}selected{/if}>{$language.name}</option>
                        {/foreach}
                    </select>
                </div>

                <div class="form-group col-3">
                    <label>{lang_form_settingsreg}</label>
                    <select name="registrations" class="form-control">
                        <option value="1" {if $data.system.registrations < 2}selected{/if}>{lang_form_enable}</option>
                        <option value="2" {if $data.system.registrations > 1}selected{/if}>{lang_form_disable}</option>  
                    </select>
                </div>

                <div class="form-group col-3">
                    <label>{lang_form_supportchat}</label>
                    <select name="livechat" class="form-control">
                        <option value="1" {if $data.system.livechat < 2}selected{/if}>{lang_form_enable}</option>
                        <option value="2" {if $data.system.livechat > 1}selected{/if}>{lang_form_disable}</option>  
                    </select>
                </div>

                <div class="form-group mb-0 col-12">
                    <h2 class="text-uppercase">{lang_form_settingsmailing}</h2>
                </div>

                <div class="form-group col-4">
                    <label>{lang_form_settingsmailfunc}</label>
                    <select name="mail_function" class="form-control">
                        <option value="1" {if $data.system.mail_function < 2}selected{/if}>{lang_form_native}</option>
                        <option value="2" {if $data.system.mail_function > 1}selected{/if}>{lang_form_remotesmtp}</option> 
                    </select>
                </div>

                <div class="form-group col-4">
                    <label>{lang_form_settingssitemail}</label>
                    <input type="text" name="site_mail" class="form-control" placeholder="eg. noreply@domain.com" value="{$data.system.site_mail}">
                </div>

                <div class="form-group col-4">
                    <label>{lang_form_settingssmtphost}</label>
                    <small class="text-muted">
                        {lang_form_settingssmtp_small}
                    </small>
                    <input type="text" name="smtp_host" class="form-control" placeholder="eg. smtp.gmail.com" value="{$data.system.smtp_host}">
                </div>

                <div class="form-group col-4">
                    <label>{lang_form_settingssmtpport}</label>
                    <small class="text-muted">
                        {lang_form_settingssmtp_small}
                    </small>
                    <input type="text" name="smtp_port" class="form-control" placeholder="eg. 587" value="{$data.system.smtp_port}">
                </div>

                <div class="form-group col-4">
                    <label>{lang_form_settingssmtpusername}</label>
                    <small class="text-muted">
                        {lang_form_settingssmtp_small}
                    </small>
                    <input type="text" name="smtp_username" class="form-control" placeholder="eg. username@gmail.com" value="{$data.system.smtp_username}">
                </div>

                <div class="form-group col-4">
                    <label>{lang_form_settingssmtppassword}</label>
                    <small class="text-muted">
                        {lang_form_settingssmtp_small}
                    </small>
                    <input type="password" name="smtp_password" class="form-control" placeholder="Your gmail acount password" value="{$data.system.smtp_password}">
                </div>

                <div class="form-group mb-0 col-12">
                    <h2 class="text-uppercase">{lang_form_settingspayments}</h2>
                </div>

                <div class="form-group col-6">
                    <label>{lang_form_settingspaypalusername}</label>
                    <input type="text" name="paypal_username" class="form-control" placeholder="eg. sb-mwaqs1493596_api1.business.example.com" value="{$data.system.paypal_username}">
                </div>

                <div class="form-group col-6">
                    <label>{lang_form_settingspaypalpassword}</label>
                    <input type="text" name="paypal_password" class="form-control" placeholder="eg. 7UKY8ZAEPAEE5PAP" value="{$data.system.paypal_password}">
                </div>

                <div class="form-group col-6">
                    <label>{lang_form_settingspaypalsignat}</label>
                    <input type="text" name="paypal_signature" class="form-control" placeholder="eg. AyO0M6tWEB-x5HkouybTsYW4IZ5HAtqfmPlh6qBzLT5dlAy25ZQthC86" value="{$data.system.paypal_signature}">
                </div>

                <div class="form-group col-6">
                    <label>{lang_form_settingspaypaltest}</label>
                    <select name="paypal_test" class="form-control">
                        <option value="1" {if $data.system.paypal_test < 2}selected{/if}>{lang_form_enable}</option>
                        <option value="2" {if $data.system.paypal_test > 1}selected{/if}>{lang_form_disable}</option> 
                    </select>
                </div>

                <div class="form-group col-6">
                    <label>{lang_form_settingsstripekey}</label>
                    <input type="text" name="stripe_key" class="form-control" placeholder="eg. pk_test_7rQQKZVTtFo6eXZJOhlIxoHp" value="{$data.system.stripe_key}">
                </div>

                <div class="form-group col-6">
                    <label>{lang_form_settingsstripesecret}</label>
                    <input type="text" name="stripe_secret" class="form-control" placeholder="eg. sk_test_qYQoMZbSEGecSXg2Z1R8BjV000rMDLtTGZ" value="{$data.system.stripe_secret}">
                </div>

                <div class="form-group col-6">
                    <label>{lang_form_settingsmolliekey}</label>
                    <input type="text" name="mollie_key" class="form-control" placeholder="eg. test_4vzuJVhRCFSyNyuc6gwvJpQ474Vngp" value="{$data.system.mollie_key}">
                </div>

                <div class="form-group col-3">
                    <label>{lang_form_settingscurrency}</label>
                    <select name="currency" class="form-control">
                        <option value="usd" {if $data.system.currency eq "usd"}selected{/if}>US Dollar</option>
                        <option value="eur" {if $data.system.currency eq "eur"}selected{/if}>European Euro</option> 
                        <option value="gbp" {if $data.system.currency eq "gbp"}selected{/if}>British Pound</option> 
                        <option value="aud" {if $data.system.currency eq "aud"}selected{/if}>Australian Dollar</option> 
                        <option value="cad" {if $data.system.currency eq "cad"}selected{/if}>Canadian Dollar</option> 
                        <option value="hkd" {if $data.system.currency eq "hkd"}selected{/if}>Hong Kong Dollar</option> 
                        <option value="jpy" {if $data.system.currency eq "jpy"}selected{/if}>Japanese Yen</option> 
                        <option value="rub" {if $data.system.currency eq "rub"}selected{/if}>Russian Ruble</option> 
                        <option value="sgd" {if $data.system.currency eq "sgd"}selected{/if}>Singapore Dollar</option> 
                    </select>
                </div>

                <div class="form-group col-3">
                    <label>{lang_form_settingsenabledproviders}</label>
                    <select name="providers[]" class="form-control" multiple>
                        <option value="paypal" {if $data.providers.paypal}selected{/if}>PayPal</option>
                        <option value="stripe" {if $data.providers.stripe}selected{/if}>Stripe</option> 
                        <option value="mollie" {if $data.providers.mollie}selected{/if}>Mollie</option> 
                    </select>
                </div>

                <div class="form-group mb-0 col-12">
                    <h2 class="text-uppercase">{lang_form_settingssecurity}</h2>
                </div>

                <div class="form-group col-6">
                    <label>{lang_form_settingsrecaptchakey}</label>
                    <input type="text" name="recaptcha_key" class="form-control" placeholder="eg. 6LdUnjsUAAAAAIZl-oWXpQmJPvzLG9geMsKxHdwE" value="{$data.system.recaptcha_key}">
                </div>

                <div class="form-group col-6">
                    <label>{lang_form_settingsrecaptchasecret}</label>
                    <input type="text" name="recaptcha_secret" class="form-control" placeholder="eg. 6LdUnjsUAAAAAL0_mWF1-BzlSL1r5O_IT_Jr_vMD" value="{$data.system.recaptcha_secret}">
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-lg btn-primary">
                <i class="la la-check-circle la-lg"></i> {lang_btn_save}
            </button>
        </div>
    </div>
</form>