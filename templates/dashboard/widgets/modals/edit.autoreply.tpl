<form zender-form>
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="la la-reply la-lg"></i> {$title}
            </h3>

            <button type="button" class="close" data-dismiss="modal">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        
        <div class="modal-body">
            <div class="form-row">
                <div class="form-group col-12">
                    <label>{lang_form_name}</label>
                    <input type="text" name="name" class="form-control" placeholder="eg. Auto Respond" value="{$data.autoreply.name}">
                </div>

                <div class="form-group col-12">
                    <label>{lang_form_devices}</label>
                    <select name="devices[]" class="form-control" data-live-search="true" zender-select-devices multiple>
                        <option value="0" data-tokens="auto automatic" {if $data.automatic}selected{/if}>{lang_form_automatic}</option>
                        {foreach $data.devices as $device}
                        <option value="{$device@key}" data-tokens="{$device.token}" {if $device.selected}selected{/if}>{$device.name}</option>
                        {/foreach}
                    </select>
                </div>

                <div class="form-group col-12">
                    <label>{lang_form_autoreply_keywords}</label>
                    <textarea name="keywords" class="form-control" placeholder="eg. thank you, okay, subscribe, yes">{$data.autoreply.keywords}</textarea>
                </div>

                <div class="form-group col-12">
                    <label>{lang_form_autoreply_message}</label>
                    <textarea name="message" class="form-control" rows="5" placeholder="eg. Your reply message here, shortcodes accepted">{$data.autoreply.message}</textarea>
                </div>

                <div class="form-group col-12">
                    <label>{lang_form_shortcodes}</label>
                    {literal}
                    <p>
                        <strong>
                            {{recipient.number}}, {{recipient.message}}, {{date.time}}
                        </strong>
                    </p>
                    {/literal}
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