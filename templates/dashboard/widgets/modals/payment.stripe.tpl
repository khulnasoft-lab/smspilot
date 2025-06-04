<form zender-form>
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="la la-cash-register la-lg"></i> {$title}
            </h3>

            <button type="button" class="close" data-dismiss="modal">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        
        <div class="modal-body">
            <div class="p-3">
                <div class="form-row">
                    <div class="form-group col-12 ">
                        <label>{lang_form_cardnumber}</label>
                        <div id="card-number" class="form-control"></div>
                    </div>

                    <div class="form-group col-md-6 col-sm-12">
                        <label>{lang_form_cardexpiry}</label>
                        <div id="card-expiry" class="form-control"></div>
                    </div>

                    <div class="form-group col-md-6 col-sm-12">
                        <label>{lang_form_cardcvc}</label>
                        <div id="card-cvc" class="form-control"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <input type="hidden" name="provider" value="{$data.provider}">
            <button type="submit" class="btn btn-lg btn-primary btn-block">
                <i class="la la-credit-card la-lg"></i> {lang_btn_purchase}
            </button>
        </div>
    </div>
</form>