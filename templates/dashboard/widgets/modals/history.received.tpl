<form smspilot-form>
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="la la-calendar la-lg"></i> {$title}
            </h3>

            <button type="button" class="close" data-dismiss="modal">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        
        <div class="modal-body">
            <div class="form-row">
                <div class="form-group col-12">
                    <label>{lang_form_daterange}</label>
                    <input type="text" name="date" class="form-control" placeholder="{lang_form_daterange}" smspilot-datepicker>
                </div>

                <div class="form-group col-12">
                    <label>{lang_form_number}</label>
                    <input type="text" name="phone" class="form-control" placeholder="eg. +639123456789" smspilot-autocomplete="contacts">
                </div>

                <div class="form-group col-12">
                    <label>{lang_form_device}</label>
                    <select name="device" class="form-control" data-live-search="true">
                        <option value="all" data-tokens="all" selected>{lang_form_alldevices}</option>
                        {foreach $data.devices as $device}
                        <option value="{$device@key}" data-tokens="{$device.token}">{$device.name}</option>
                        {/foreach}
                    </select>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-lg btn-primary btn-block">
                <i class="la la-search la-lg"></i> {lang_btn_search}
            </button>
        </div>
    </div>
</form>