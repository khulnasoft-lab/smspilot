<form smspilot-form>
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="la la-user-plus la-lg"></i> {$title}
            </h3>

            <button type="button" class="close" data-dismiss="modal">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        
        <div class="modal-body">
            <div class="form-row">
                <div class="form-group col-12">
                    <label>{lang_form_name}</label>
                    <input type="text" name="name" class="form-control" placeholder="eg. Michael Cors">
                </div>

                <div class="form-group col-12">
                    <label>{lang_form_emailaddress}</label>
                    <input type="text" name="email" class="form-control" placeholder="eg. user@mail.com">
                </div>
                
                <div class="form-group col-12">
                    <label>{lang_form_password}</label>
                    <input type="text" name="password" class="form-control" placeholder="eg. {lang_form_password}">
                </div>

                <div class="form-group col-12">
                    <label>{lang_form_adduser_role}</label>
                    <select name="role" class="form-control">
                        {foreach $data.roles as $role}
                        <option value="{$role@key}">{$role.name}</option>
                        {/foreach}
                    </select>
                </div>

                <div class="form-group col-12">
                    <label>{lang_form_language}</label>
                    <select name="language" class="form-control" data-live-search="true">
                        {foreach $data.languages as $language}
                        <option value="{$language@key}" data-tokens="{$language.token}">{$language.name}</option>
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