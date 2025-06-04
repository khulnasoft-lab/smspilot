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
                    <div class="form-group col-12">
                        <label>{lang_form_voucher}</label>
                        <input type="text" name="code" class="form-control" placeholder="eg. 2f5b4727c7c8596779b21d7644e2ebb8">
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <input type="hidden" name="provider" value="{$data.provider}">
            <button type="submit" class="btn btn-lg btn-primary btn-block">
                <i class="la la-check-circle la-lg"></i> {lang_btn_redeem}
            </button>
        </div>
    </div>
</form>