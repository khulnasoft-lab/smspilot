<div class="modal-content">
    <div class="modal-header">
        <h3 class="modal-title">
            <i class="la la-digital-tachograph la-lg"></i> {$title}
        </h3>

        <button type="button" class="close" data-dismiss="modal">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    
    <div class="modal-body">
        {if $data.providers.paypal}
        <button class="btn btn-lg btn-primary btn-block" zender-toggle="zender.payment/{$data.package.id}/paypal">
            <i class="la la-paypal la-lg"></i> {lang_pay_with_paypal}
        </button>
        {/if}

        {if $data.providers.stripe}
        <button class="btn btn-lg btn-primary btn-block" zender-toggle="zender.payment/{$data.package.id}/stripe">
            <i class="la la-cc-stripe la-lg"></i> {lang_pay_with_stripe}
        </button>
        {/if}

        {if $data.providers.mollie}
        <button class="btn btn-lg btn-primary btn-block" mollie-package="{$data.package.id}" zender-action="mollie">
            <i class="la la-euro-sign la-lg"></i> {lang_pay_with_mollie}
        </button>
        {/if}

        <button class="btn btn-lg btn-primary btn-block" zender-toggle="zender.payment/{$data.package.id}/voucher">
            <i class="la la-money-bill-wave la-lg"></i> {lang_pay_with_voucher}
        </button>
    </div>
</div>