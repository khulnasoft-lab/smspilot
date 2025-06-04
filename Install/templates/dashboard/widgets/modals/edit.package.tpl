<form zender-form>
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="la la-cube la-lg"></i> {$title}
            </h3>

            <button type="button" class="close" data-dismiss="modal">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        
        <div class="modal-body">
            <div class="form-row">
                <div class="form-group col-6">
                    <label>{lang_form_name}</label>
                    <input type="text" name="name" class="form-control" placeholder="eg. VIP Package" value="{$data.package.name}">
                </div>

                <div class="form-group col-lg-6">
                    <label>{lang_form_packageprice}</label>
                    <small class="text-muted">
                        {lang_form_inusd}
                    </small>
                    <input type="text" name="price" class="form-control" placeholder="eg. 25" value="{$data.package.price}">
                </div>

                <div class="form-group col-lg-6">
                    <label>{lang_form_packagesend}</label>
                    <small class="text-muted">
                        {lang_form_perday}
                    </small>
                    <input type="text" name="send" class="form-control" placeholder="eg. 300" value="{$data.package.send_limit}">
                </div>

                <div class="form-group col-lg-6">
                    <label>{lang_form_packagereceive}</label>
                    <small class="text-muted">
                        {lang_form_perday}
                    </small>
                    <input type="text" name="receive" class="form-control" placeholder="eg. 150" value="{$data.package.receive_limit}">
                </div>

                <div class="form-group col-lg-6">
                    <label>{lang_form_package_contactslimit}</label>
                    <input type="text" name="contact" class="form-control" placeholder="eg. 25" value="{$data.package.contact_limit}">
                </div>

                <div class="form-group col-lg-6">
                    <label>{lang_form_packagedevice}</label>
                    <input type="text" name="device" class="form-control" placeholder="eg. 25" value="{$data.package.device_limit}">
                </div>

                <div class="form-group col-lg-6">
                    <label>{lang_form_packagekey}</label>
                    <input type="text" name="key" class="form-control" placeholder="eg. 10" value="{$data.package.key_limit}">
                </div>

                <div class="form-group col-lg-6">
                    <label>{lang_form_packagehook}</label>
                    <input type="text" name="webhook" class="form-control" placeholder="eg. 5" value="{$data.package.webhook_limit}">
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