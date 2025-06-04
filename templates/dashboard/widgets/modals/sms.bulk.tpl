<form smspilot-form>
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="la la-mail-bulk la-lg"></i> {$title}
            </h3>

            <button type="button" class="close" data-dismiss="modal">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        
        <div class="modal-body">
            <div class="form-row">
                <div class="form-group col-6">
                    <label>{lang_form_groups}</label>
                    <select name="groups[]" class="form-control" data-live-search="true" multiple>
                        {foreach $data.groups as $group}
                        <option value="{$group@key}" data-tokens="{$group.token}" {if $group.default}selected{/if}>{$group.name}</option>
                        {/foreach}
                    </select>
                </div>

                <div class="form-group col-6">
                    <div class="form-group">
                        <label>{lang_form_bulksms_numbers}</label>
                        <textarea name="numbers" class="form-control" rows="3" placeholder="Multiple E.164 numbers separated by line break
+639123456789
+639123456788
+639123456787
+639123456786
+639123456785
+639123456784
+639123456783
+639123456782
+639123456781
"></textarea>
                    </div>
                </div>
                
                <div class="form-group col-4">
                    <label>{lang_form_device}</label>
                    <select name="device" class="form-control" data-live-search="true">
                        <option value="0" data-tokens="auto automatic" selected>{lang_form_automatic}</option>
                        {foreach $data.devices as $device}
                        <option value="{$device@key}" data-tokens="{$device.token}">{$device.name}</option>
                        {/foreach}
                    </select>
                </div>

                <div class="form-group col-4">
                    <label>{lang_form_sim}</label>
                    <select name="sim" class="form-control">
                        <option value="0" selected>SIM1</option>
                        <option value="1">SIM2</option>
                    </select>
                </div>

                <div class="form-group col-4">
                    <label>{lang_form_priority}</label>
                    <select name="priority" class="form-control">
                        <option value="0" selected>{lang_form_no}</option>
                        <option value="1">{lang_form_yes}</option>
                    </select>
                </div>

                <div class="form-group col-4">
                    <label>{lang_form_template}</label>
                    <select class="form-control" data-live-search="true" smspilot-select-template>
                        <option value="none" data-tokens="no none 0" selected>{lang_form_none}</option>
                        {foreach $data.templates as $template}
                        <option value="{$template@key}" data-tokens="{$template.token}" data-format="{$template.format}">{$template.name}</option>
                        {/foreach}
                    </select>
                </div>

                <div class="form-group col-8">
                    <div class="form-group">
                        <label>{lang_form_message}</label>
                        <textarea name="message" class="form-control" rows="7" placeholder="{lang_form_message_placeholder}"></textarea>
                    </div>

                    <div class="form-group">
                        <label>{lang_form_shortcodes}</label>
                        {literal}
                        <p>
                            <strong>
                                {{contact.name}}, {{contact.number}}, {{group.name}}, {{date.now}}, {{date.time}}
                            </strong>
                        </p>
                        {/literal}
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-lg btn-primary">
                <i class="la la-telegram la-lg"></i> {lang_btn_send}
            </button>
        </div>
    </div>
</form>