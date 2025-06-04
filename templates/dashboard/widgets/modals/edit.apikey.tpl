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
                    <input type="text" name="name" class="form-control" placeholder="eg. Remote Sender" value="{$data.key.name}">
                </div>

                <div class="form-group col-12">
                    <label>{lang_form_devices}</label>
                    <select name="devices[]" class="form-control" data-live-search="true" smspilot-select-devices multiple>
                        <option value="0" data-tokens="auto automatic" {if $data.automatic}selected{/if}>{lang_form_automatic}</option>
                        {foreach $data.devices as $device}
                        <option value="{$device@key}" data-tokens="{$device.token}" {if $device.selected}selected{/if}>{$device.name}</option>
                        {/foreach}
                    </select>
                </div>

                <div class="form-group col-12">
                    <label>{lang_form_permissions}</label>
                    <select name="permissions[]" class="form-control" data-live-search="true" multiple>
                        {foreach $data.permissions as $permission}
                        <option value="{$permission@key}" data-tokens="{$permission.token}" {if $permission.selected}selected{/if}>{$permission.name}</option>
                        {/foreach}
                    </select>
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