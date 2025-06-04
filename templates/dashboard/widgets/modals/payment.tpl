<form smspilot-form>
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
            <div class="mb-3">
                <smspilot-creditcard></smspilot-creditcard>
            </div>

            <div class="p-3">
                <div class="form-row">
                    <div class="form-group col-md-6 col-sm-12">
                        <label>{lang_form_cardnumber}</label>
                        <input type="text" name="number" class="form-control" placeholder="eg. 1234 5678 9123 4567">
                    </div>

                    <div class="form-group col-md-6 col-sm-12">
                        <label>{lang_form_cardexpiry}</label>
                        <input type="text" name="expiry" class="form-control" placeholder="eg. MM/YY">
                    </div>

                    <div class="form-group col-md-6 col-sm-12">
                        <label>{lang_form_cardname}</label>
                        <input type="text" name="name" class="form-control" placeholder="eg. Firstname Lastname">
                    </div>

                    <div class="form-group col-md-6 col-sm-12">
                        <label>{lang_form_cardcvc}</label>
                        <input type="text" name="cvc" class="form-control" placeholder="eg. 099">
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