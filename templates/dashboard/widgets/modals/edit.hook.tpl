<form smspilot-form>
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="la la-wave-square la-lg"></i> {$title}
            </h3>

            <button type="button" class="close" data-dismiss="modal">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        
        <div class="modal-body">
            <div class="form-row">
                <div class="form-group col-12">
                    <label>{lang_form_name}</label>
                    <input type="text" name="name" class="form-control" placeholder="eg. Test Action" value="{$data.hook.name}">
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
                    <label>{lang_form_hook_event}</label>
                    <select name="event" class="form-control">
                        <option value="1" {if $data.hook.event < 2}selected{/if}>On Send</option>
                        <option value="2" {if $data.hook.event > 1}selected{/if}>On Receive</option>
                    </select>
                </div>

                <div class="form-group col-12">
                    <label>{lang_form_hook_link}</label>
                    <textarea name="link" rows="5" class="form-control">{$data.hook.link}</textarea>
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