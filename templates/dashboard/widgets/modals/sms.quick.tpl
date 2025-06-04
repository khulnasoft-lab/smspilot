<form smspilot-form>
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="la la-telegram la-lg"></i> {$title}
            </h3>

            <button type="button" class="close" data-dismiss="modal">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        
        <div class="modal-body">
            <div class="form-row">
                <div class="form-group col-12">
                    <label>{lang_form_number}</label>
                    <input type="text" name="phone" class="form-control" placeholder="eg. +639123456789" smspilot-autocomplete="contacts">
                </div>
                
                <div class="form-group col-12">
                    <label>{lang_form_device}</label>
                    <select name="device" class="form-control" data-live-search="true">
                        <option value="0" data-tokens="auto automatic" selected>{lang_form_automatic}</option>
                        {foreach $data.devices as $device}
                        <option value="{$device@key}" data-tokens="{$device.token}">{$device.name}</option>
                        {/foreach}
                    </select>
                </div>

                <div class="form-group col-12">
                    <label>{lang_form_sim}</label>
                    <select name="sim" class="form-control">
                        <option value="0" selected>SIM1</option>
                        <option value="1">SIM2</option>
                    </select>
                </div>

                <div class="form-group col-12">
                    <label>{lang_form_priority}</label>
                    <select name="priority" class="form-control">
                        <option value="0" selected>{lang_form_no}</option>
                        <option value="1">{lang_form_yes}</option>
                    </select>
                </div>

                <div class="form-group col-12">
                    <label>{lang_form_message}</label>
                    <textarea name="message" class="form-control" rows="5" placeholder="{lang_form_message_placeholder}"></textarea>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-lg btn-primary btn-block">
                <i class="la la-telegram la-lg"></i> {lang_btn_send}
            </button>
        </div>
    </div>
</form>