<form smspilot-form>
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="la la-key la-lg"></i> {$title}
            </h3>

            <button type="button" class="close" data-dismiss="modal">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        
        <div class="modal-body">
            <div class="form-row">
                <div class="form-group col-12">
                    <label>{lang_form_name}</label>
                    <input type="text" name="name" class="form-control" placeholder="eg. Remote Sender">
                </div>

                <div class="form-group col-12">
                    <label>{lang_form_devices}</label>
                    <select name="devices[]" class="form-control" data-live-search="true" smspilot-select-devices multiple>
                        <option value="0" data-tokens="auto automatic" selected>{lang_form_automatic}</option>
                        {foreach $data.devices as $device}
                        <option value="{$device@key}" data-tokens="{$device.token}">{$device.name}</option>
                        {/foreach}
                    </select>
                </div>

                <div class="form-group col-12">
                    <label>{lang_form_permissions}</label>
                    <select name="permissions[]" class="form-control" data-live-search="true" multiple>
                        <option value="get_pending">get_pending</option>
                        <option value="get_received">get_received</option>
                        <option value="get_sent">get_sent</option>
                        <option value="send" selected>send</option>
                        <option value="get_contacts" selected>get_contacts</option>
                        <option value="get_groups">get_groups</option>
                        <option value="create_contact">create_contact</option>
                        <option value="create_group">create_group</option>
                        <option value="delete_contact">delete_contact</option>
                        <option value="delete_group">delete_group</option>
                        <option value="get_device">get_device</option>
                        <option value="get_devices" selected>get_devices</option>
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