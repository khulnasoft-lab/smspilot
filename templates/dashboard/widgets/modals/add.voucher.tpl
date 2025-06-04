<form smspilot-form>
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="la la-money-bill-wave la-lg"></i> {$title}
            </h3>

            <button type="button" class="close" data-dismiss="modal">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        
        <div class="modal-body">
            <div class="form-row">
                <div class="form-group col-12">
                    <label>{lang_form_name}</label>
                    <input type="text" name="name" class="form-control" placeholder="eg. Special">
                </div>

                <div class="form-group col-12">
                    <label>{lang_form_voucher_package}</label>
                    <select name="package" class="form-control" data-live-search="true">
                        {foreach $data.packages as $package}
                            {if $package.id > 1}
                            <option value="{$package@key}" data-tokens="{strtolower($package.name)}">{$package.name}</option>
                            {/if}
                        {/foreach}
                    </select>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-lg btn-primary btn-block">
                <i class="la la-check-circle la-lg"></i> {lang_btn_submit}
            </button>
        </div>
    </div>
</form>