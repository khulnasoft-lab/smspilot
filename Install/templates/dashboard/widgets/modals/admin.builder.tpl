<form zender-form>
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="la la-tools la-lg"></i> {$title}
            </h3>

            <button type="button" class="close" data-dismiss="modal">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        
        <div class="modal-body">
            <div class="form-row">
                <div class="col-12">
                    <div class="alert alert-danger text-center">
                        {lang_form_builderalert}
                    </div>
                </div>

                <div class="form-group col-4">
                    <label>{lang_form_builderpackagename}</label>
                    <small class="text-danger">
                        {lang_form_builderpackagename_unique}
                    </small>
                    <input type="text" name="package_name" class="form-control" placeholder="eg. com.gateway.sample" value="{$data.builder.package_name}">
                </div>

                <div class="form-group col-4">
                    <label>{lang_form_builderappname}</label>
                    <small class="text-danger">
                        {lang_form_required}
                    </small>
                    <input type="text" name="app_name" class="form-control" placeholder="eg. Zender Gateway" value="{$data.builder.app_name}">
                </div>

                <div class="form-group col-4">
                    <label>{lang_form_builderappdesc}</label>
                    <small class="text-primary">
                        {lang_form_optional}
                    </small>
                    <input type="text" name="app_desc" class="form-control" placeholder="eg. {lang_form_builderappdesc_placeholder}" value="{$data.builder.app_desc}">
                </div>

                <div class="form-group col-6">
                    <label>{lang_form_builderappcolor}</label>
                    <small class="text-danger">
                        {lang_form_buildershouldmatch}
                    </small>
                    <input type="color" name="app_color" class="form-control" placeholder="eg. #000000" value="{$data.builder.app_color}">
                </div>

                <div class="form-group col-6">
                    <label>{lang_form_buildersend}</label>
                    <small class="text-danger">
                        {lang_form_buildersend_sec}
                    </small>
                    <input type="number" name="app_send" class="form-control" placeholder="eg. 5" value="{$data.builder.app_send}">
                </div>

                <div class="form-group col-6">
                    <label>{lang_form_builderreceive}</label>
                    <small class="text-danger">
                        {lang_form_builderreceive_sec}
                    </small>
                    <input type="number" name="app_receive" class="form-control" placeholder="eg. 60" value="{$data.builder.app_receive}">
                </div>

                <div class="form-group col-6">
                    <label>{lang_form_builderemail}</label>
                    <small class="text-danger">
                        {lang_form_builderemail_small}
                    </small>
                    <input type="text" name="builder_email" class="form-control" placeholder="eg. mail@gmail.com" value="{$data.builder.builder_email}">
                </div>

                <div class="form-group col-12">
                    <label>{lang_form_builderapplogo}</label>
                    <small class="text-danger">
                       {lang_form_builderapplogo_logoimg} {if $data.assets.logo}<b class="text-success">({lang_form_uploaded})</b>{else}<b>({lang_form_notuploaded})</b>{/if}
                    </small>
                    <input type="file" name="app_logo" class="form-control pb-5">
                </div>

                <div class="form-group col-6">
                    <label>{lang_form_builderappicon}</label>
                    <small class="text-danger">
                       {lang_form_builderappicon_iconimg} {if $data.assets.icon}<b class="text-success">({lang_form_uploaded})</b>{else}<b>({lang_form_notuploaded})</b>{/if}
                    </small>
                    <input type="file" name="app_icon" class="form-control pb-5">
                </div>

                <div class="form-group col-6">
                    <label>{lang_form_builderappsplash}</label>
                    <small class="text-danger">
                        {lang_form_builderappsplash_splashimg} {if $data.assets.splash}<b class="text-success">({lang_form_uploaded})</b>{else}<b>({lang_form_notuploaded})</b>{/if}
                    </small>
                    <input type="file" name="app_splash" class="form-control pb-5">
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-lg btn-primary">
                <i class="la la-check-circle la-lg"></i> {lang_btn_submit}
            </button>
        </div>
    </div>
</form>