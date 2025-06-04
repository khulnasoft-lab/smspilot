<form zender-form>
    <div class="modal-content">
        <div class="modal-header border-0">
            <button type="button" class="close" data-dismiss="modal">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <div class="login-page">
            <h1 class="text-center text-uppercase mb-1">{$title}</h1>
            
            <div class="form">
                <div class="flex center vcenter">
                    <input type="text" name="email" class="input input-primary" placeholder="{lang_form_emailaddress}">
                </div>

                {if !empty(system_recaptcha_key)}
                <div class="flex center vcenter mb-1">
                    <div class="g-recaptcha" data-sitekey="{system_recaptcha_key}"></div>
                </div>
                {/if}

                <div class="flex center">
                    <button type="submit" class="btn btn-primary btn-lg text-uppercase">
                        {lang_btn_retrieve}
                    </button>
                </div>

                <div class="flex center">
                    <a href="#" class="text-muted text-uppercase mt-4" zender-toggle="zender.login">{lang_form_alreadyremember}</a>
                </div>
            </div>
        </div>
    </div>
</form>

{if !empty(system_recaptcha_key)}
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
{/if}