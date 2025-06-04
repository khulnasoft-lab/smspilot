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
                <div class="form-group col-md-5">
                    <label>
                        {__("lang_form_name")} <i class="la la-info-circle" title="{__("lang_and_dash_auto_line17")}"></i>
                    </label>
                    <input type="text" name="name" class="form-control" placeholder="{__("lang_and_dash_hook_line19")}">
                </div>

                <div class="form-group col-md-3">
                    <label>
                        {__("lang_and_dash_auto_line24")} <i class="la la-info-circle" title="{__("lang_and_dash_auto_line24_1")}"></i>
                    </label>
                    <select name="source" class="form-control">
                        <option value="1">{__("lang_and_dash_auto_line27")}</option>
                        <option value="2">{__("lang_and_dash_auto_line28")}</option>
                    </select>
                </div>

                <div class="form-group col-md-4">
                    <label>
                        {__("lang_form_autoreply_modal_widget_matchtype")} <i class="la la-info-circle" title="{__("lang_form_autoreply_modal_widget_matchtype_desc")}"></i>
                    </label>
                    <select name="match" class="form-control">
                        <option value="1">{__("lang_form_autoreply_modal_widget_matchtype_sel1")}</option>
                        <option value="2">{__("lang_form_autoreply_modal_widget_matchtype_sel2")}</option>
                        <option value="3" selected>{__("lang_form_autoreply_modal_widget_matchtype_sel3")}</option>
                        <option value="4">{__("lang_form_autoreply_modal_widget_matchtype_sel4")}</option>
                    </select>
                </div>

                <div class="form-group col-4">
                    <div zender-autoreply-device>
                        <label>
                            {__("lang_form_device")} <i class="la la-info-circle" title="{__("lang_form_autoreply_modal_widget_devicedesc_38")}"></i>
                        </label>
                        <select name="device" class="form-control" data-live-search="true">
                            {foreach $data.devices as $device}
                            <option value="{$device@key}" data-tokens="{$device.token}">{$device.name}</option>
                            {/foreach}
                        </select>

                        <label>
                            {__("lang_form_autoreply_modal_widget_simslot_38")} <i class="la la-info-circle" title="{__("lang_form_autoreply_modal_widget_simslot_38_desc")}"></i>
                        </label>
                        <select name="sim" class="form-control">
                            <option value="1" selected>{__("lang_and_sms_quick49")}</option>
                            <option value="2">{__("lang_and_sms_quick50")}</option>
                        </select>
                    </div>

                    <div zender-autoreply-whatsapp>
                        <label>
                            {__("lang_and_whatquick_36")} <i class="la la-info-circle" title="{__("lang_form_autoreply_modal_widget_waaccount_38_desc")}"></i>
                        </label>
                        <select name="account" class="form-control" data-live-search="true" zender-wa-account-select>
                            {foreach $data.accounts as $account}
                            <option value="{$account@key}" data-tokens="{$account.token}">{$account.name}</option>
                            {/foreach}
                        </select>

                        <label>
                            {__("lang_form_autoreply_modal_widget_priority_38")} <i class="la la-info-circle" title="{__("lang_form_autoreply_modal_widget_priority_38_desc")}"></i>
                        </label>
                        <select name="priority" class="form-control">
                            <option value="1">{__("lang_form_yes")}</option>
                            <option value="2" selected>{__("lang_form_no")}</option>
                        </select>

                        <label>
                            {__("lang_forms_whatsapp_messagetype")} <i class="la la-info-circle" title="{__("lang_forms_whatsapp_messagetypehelp")}"></i>
                        </label>
                        <select name="message_type" class="form-control" zender-wa-type>
                            <option value="text" selected>{__("lang_forms_whatsapp_typetext")}</option>
                            <option value="media">{__("lang_forms_whatsapp_typemedia")}</option>
                            <option value="document">{__("lang_forms_whatsapp_typedoc")}</option>
                        </select>

                        <div zender-wa-type-media>
                            <label>
                                {__("lang_forms_whatsapp_mediafile")} <i class="la la-info-circle" title="{__("lang_forms_whatsapp_mediafilehelp38")}"></i>
                            </label>
                            <input type="file" name="media_file" class="form-control pb-5">
                        </div>

                        <div zender-wa-type-document>
                            <label>
                                {__("lang_forms_whatsapp_docfile")} <i class="la la-info-circle" title="{__("lang_forms_whatsapp_docfilehelp")}"></i>
                            </label>
                            <input type="file" name="doc_file" class="form-control pb-5">
                        </div>
                    </div>
                </div>

                <div class="form-group col-8">
                    <label>
                        {__("lang_form_autoreply_modal_widget_trigger_38")} <i class="la la-info-circle" title="{__("lang_form_autoreply_modal_widget_trigger_38_desc")}"></i>
                    </label>
                    <textarea name="keywords" class="form-control mb-3" placeholder="{__("lang_and_dash_auto_line36")}"></textarea>
                    
                    <div zender-wa-audio-hide>
                        <label>
                            {__("lang_form_autoreply_message")} <i class="la la-info-circle" title="{__("lang_and_dash_auto_line41")}"></i>
                        </label>
                        <textarea name="message" class="form-control mb-3" rows="5" placeholder="{__("lang_and_dash_auto_line43")}"></textarea>

                        <label>
                            {__("lang_form_shortcodes")} <i class="la la-info-circle" title="{__("lang_and_dash_auto_line48")}"></i>
                        </label>
                        {literal}
                        <p>
                            <code>
                                <strong>{{phone}}</strong>, <strong>{{message}}</strong>, <strong>{{date.now}}</strong>, <strong>{{date.time}}</strong>
                            </code>
                        </p>
                        {/literal}
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-lg btn-primary">
                <i class="la la-check-circle la-lg"></i> {__("lang_btn_submit")}
            </button>
        </div>
    </div>
</form>