<form zender-form>
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="la la-file-excel la-lg"></i> {$title}
            </h3>

            <button type="button" class="close" data-dismiss="modal">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        
        <div class="modal-body">
            <div class="form-row">
                <div class="form-group col-12">
                    <label>{lang_form_excelfile}</label>
                    <small class="text-danger">
                        {lang_form_followformat} <a href="#" zender-toggle="zender.view/excel-1">{lang_form_clickhere}</a>
                    </small>
                    <input type="file" name="excel" class="form-control pb-5">
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
            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-lg btn-primary btn-block">
                <i class="la la-check-circle la-lg"></i> {lang_btn_send}
            </button>
        </div>
    </div>
</form>