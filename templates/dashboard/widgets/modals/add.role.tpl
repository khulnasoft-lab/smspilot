<form smspilot-form>
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="la la-shield la-lg"></i> {$title}
            </h3>

            <button type="button" class="close" data-dismiss="modal">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        
        <div class="modal-body">
            <div class="form-row">
                <div class="form-group col-12">
                    <label>{lang_form_name}</label>
                    <input type="text" name="name" class="form-control" placeholder="eg. Accounting">
                </div>
                
                <div class="form-group col-12">
                    <label>{lang_form_permissions}</label>
                    <select name="permissions[]" class="form-control" multiple>
                        <option value="manage_users" selected>{lang_form_roles_manageusers}</option>
                        <option value="manage_packages">{lang_form_roles_managepackages}</option>
                        <option value="manage_vouchers">{lang_form_roles_managevouchers}</option>
                        <option value="manage_subscriptions" selected>{lang_form_roles_managesubscriptions}</option>
                        <option value="manage_transactions">{lang_form_roles_managetransactions}</option>
                        <option value="manage_widgets">{lang_form_roles_managewidgets}</option>
                        <option value="manage_pages">{lang_form_roles_managepages}</option>
                        <option value="manage_languages">{lang_form_roles_managelanguages}</option>
                        <option value="manage_fields">{lang_form_roles_managefields}</option>
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