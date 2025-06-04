<form zender-form>
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
                <div class="form-group col-md-4">
                    <label>
                        {__("lang_form_name")} <i class="la la-info-circle" title="{__("lang_and_whatssched_line17")}"></i>
                    </label>
                    <input type="text" name="name" class="form-control" placeholder="{__("lang_and_whatssched_line19")}">
                </div>

                <div class="form-group col-md-4">
                    <label>
                        {__("lang_form_schedule_schedule")} <i class="la la-info-circle" title="{__("lang_and_whatssched_line24")}"></i>
                    </label>
                    <input type="text" name="schedule" class="form-control" placeholder="{__("lang_and_whatssched_line26")}" zender-datepicker-schedule>
                </div>

                <div class="form-group col-md-4">
                    <label>
                    {__("lang_and_whatssched_line79")} <i class="la la-info-circle" title="{__("lang_and_whatssched_line79_1")}"></i>
                    </label>
                    <select name="account" class="form-control" data-live-search="true">
                        {foreach $data.accounts as $account}
                        <option value="{$account@key}" data-tokens="{$account.token}">{$account.name}</option>
                        {/foreach}
                    </select>
                </div>

                <div class="form-group col-md-5">
                    <div class="form-group">
                        <label>
                            {__("lang_form_bulksms_numbers")} <i class="la la-info-circle" title="{__("lang_and_whatssched_line42")}"></i>
                        </label>
                        <textarea name="numbers" class="form-control" rows="3" placeholder="{$data.number}
{$data.number}
{$data.number}
{$data.number}
{$data.number}
"></textarea>
                    </div>
                </div>

                <div class="form-group col-md-3">
                    <label>
                        {__("lang_form_groups")} <i class="la la-info-circle" title="{__("lang_and_whatssched_line55")}"></i>
                    </label>
                    <select name="groups[]" class="form-control" data-live-search="true" zender-select-groups multiple>
                        <option value="0" data-tokens="None {__("lang_form_select_multinone")}" selected>{__("lang_form_select_multinone")}</option>
                        {foreach $data.groups as $group}
                        <option value="{$group@key}" data-tokens="{$group.token}">{$group.name}</option>
                        {/foreach}
                    </select>
                </div>

                <div class="form-group col-md-4">
                    <label>
                        {__("lang_and_whatssched_line67")} <i class="la la-info-circle" title="{__("lang_and_whatssched_line67_1")}"></i>
                    </label>
                    <select name="shortener" class="form-control">
                        <option value="0" selected>{__("lang_and_whatssched_line70")}</option>
                        {foreach $data.shorteners as $shortener}
                        <option value="{$shortener@key}">{$shortener.name}</option>
                        {/foreach}
                    </select>
                </div>

                <div class="form-group col-md-4">
                    <label>
                        {__("lang_forms_repeatdays_title")} <i class="la la-info-circle" title="{__("lang_forms_repeatdays_tagline")}"></i>
                    </label>
                    <input type="number" name="repeat" class="form-control" placeholder="ex. 7" value="0">

                    <label class="mt-3">
                        {__("lang_form_template")} <i class="la la-info-circle" title="{__("lang_and_whatsached_line88")}"></i>
                    </label>
                    <select class="form-control" data-live-search="true" zender-select-template>
                        <option value="none" data-tokens="no none 0" selected>{__("lang_form_none")}</option>
                        {foreach $data.templates as $template}
                        <option value="{$template@key}" data-tokens="{$template.token}" data-format="{$template.format}">{$template.name}</option>
                        {/foreach}
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

                <div class="form-group col-md-8">
                    <label>{__("lang_form_message")} <small class="text-muted">(<span zender-counter-view></span>{if system_message_max < 1} {__("lang_form_messagecounterchars")}{/if})</small></label>
                    <textarea name="message" class="form-control mb-3" rows="7" placeholder="{__("lang_form_message_placeholder")}" zender-counter></textarea>

                    <label>
                        {__("lang_and_sms_bulk_131")} <i class="la la-info-circle" title="{__("lang_and_sms_bulk_131_1")}"></i>
                    </label>
                    <p>
                        <small>{__("lang_and_sms_bulk_135")}</small> <code>Tom is a {literal}<strong>{good|bad}</strong>{/literal} cat</code>
                    </p>
                    <p>
                        <small>{___(__("lang_form_literal_spintaxdesc2"), ["<strong>good</strong>", "<strong>bad</strong>"])}</small>
                    </p>

                    <label>
                        {__("lang_form_shortcodes")} <i class="la la-info-circle" title="{__("lang_and_whatssched_line115")}"></i>
                    </label>
                    {literal}
                    <p>
                        <code><strong>{{contact.name}}</strong>, <strong>{{contact.number}}</strong>, <strong>{{group.name}}</strong>, <strong>{{date.now}}</strong>, <strong>{{date.time}}</strong></code>
                    </p>
                    {/literal}
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-lg btn-primary">
                <i class="la la-check-circle la-lg"></i> {__("lang_and_whatssched_line128")}
            </button>
        </div>
    </div>
</form>