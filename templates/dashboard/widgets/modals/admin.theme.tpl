<form smspilot-form>
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
                <div class="form-group col-12">
                    <label>{lang_form_theme_landlogo}</label>
                    <small class="text-danger">
                        250x50 {if $data.assets.landing}<b class="text-success">({lang_form_uploaded})</b>{else}<b>({lang_form_notuploaded})</b>{/if}
                    </small>
                    <input type="file" name="landing_img" class="form-control pb-5">
                </div>

                <div class="form-group col-12">
                    <label>{lang_form_theme_dashlogo}</label>
                    <small class="text-danger">
                        65x65 {if $data.assets.dashboard}<b class="text-success">({lang_form_uploaded})</b>{else}<b>({lang_form_notuploaded})</b>{/if}
                    </small>
                    <input type="file" name="dashboard_img" class="form-control pb-5">
                </div>

                <div class="form-group col-12">
                    <label>{lang_form_theme_favicon}</label>
                    <small class="text-danger">
                        50x50 {if $data.assets.favicon}<b class="text-success">({lang_form_uploaded})</b>{else}<b>({lang_form_notuploaded})</b>{/if}
                    </small>
                    <input type="file" name="favicon_img" class="form-control pb-5">
                </div>

                <div class="form-group col-6">
                    <label>{lang_form_themebg}</label>
                    <input type="color" name="theme_background" class="form-control" value="{$data.system.theme_background}">
                </div>

                <div class="form-group col-6">
                    <label>{lang_form_themetext}</label>
                    <input type="color" name="theme_highlight" class="form-control" value="{$data.system.theme_highlight}">
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-lg btn-primary btn-block">
                <i class="la la-check-circle la-lg"></i> {lang_btn_save}
            </button>
        </div>
    </div>
</form>