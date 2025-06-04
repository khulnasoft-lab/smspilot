<form zender-form>
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="la la-user-cog la-lg"></i> {$title}
            </h3>

            <button type="button" class="close" data-dismiss="modal">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        
        <div class="modal-body">
            <div class="form-row">
                <div class="form-group col-12">
                    <label>{lang_form_avatar}</label>
                    <input type="file" name="avatar" class="form-control pb-5">
                </div>

                <div class="form-group col-12">
                    <label>{lang_form_name}</label>
                    <input type="text" name="name" class="form-control" placeholder="eg. Michael Cors" value="{$data.user.name}">
                </div>

                <div class="form-group col-12">
                    <label>{lang_form_emailaddress}</label>
                    <input type="text" name="email" class="form-control" placeholder="eg. yourmail@mail.com" value="{$data.user.email}">
                </div>

                <div class="form-group col-12">
                    <label>{lang_form_changepass}</label>
                    <input type="password" name="password" class="form-control" placeholder="{lang_form_password_leave}">
                </div>

                <input type="hidden" name="current_email" value="{$data.user.email}">
            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-lg btn-primary btn-block">
                <i class="la la-check-circle la-lg"></i> {lang_btn_save}
            </button>
        </div>
    </div>
</form>