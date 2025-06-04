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
                        {__("lang_forms_whatsapp_phonegroupsname")} <i class="la la-info-circle" title="{__("lang_forms_whatsapp_phonegroupshelp")}"></i>
                    </label>
                    <input type="text" name="phone" class="form-control" placeholder="eg. {$data.phone}" zender-whatsapp-autocomplete>
                </div>

                <div class="form-group col-md-4">
                    <label>
                        {__("lang_and_whatquick_36")} <i class="la la-info-circle" title="{__("lang_and_whatquick_36_1")}"></i>
                    </label>
                    <select name="account" class="form-control" data-live-search="true" zender-wa-account-select>
                        {foreach $data.accounts as $account}
                        <option value="{$account@key}" data-tokens="{$account.token}">{$account.name}</option>
                        {/foreach}
                    </select>
                </div>

                <div class="form-group col-md-4">
                    <label>
                        {__("lang_and_whatquick_24")} <i class="la la-info-circle" title="{__("lang_and_whatquick_24_1")}"></i>
                    </label>
                    <select name="shortener" class="form-control">
                        <option value="0" selected>{__("lang_and_whatquick_27")}</option>
                        {foreach $data.shorteners as $shortener}
                        <option value="{$shortener@key}">{$shortener.name}</option>
                        {/foreach}
                    </select>
                </div>
 
                <div class="form-group col-md-6">
                    <label>
                        {__("lang_form_message")} <small class="text-muted">(<span zender-counter-view></span>{if system_message_max < 1} {__("lang_form_messagecounterchars")}{/if})</small>
                    </label>
                    <button class="btn btn-primary btn-sm" zender-action="translate">
                        <i class="la la-language" title="{__("lang_and_whatquick_50")}"></i>
                    </button>
                    <textarea name="message" class="form-control mb-3" rows="5" placeholder="{__("lang_form_message_placeholder")}" zender-counter></textarea>

                    <label>
                        {__("lang_and_whatquick_55")} <i class="la la-info-circle" title="{__("lang_and_whatquick_55_1")}"></i>
                    </label>
                    <p>
                        <small>{__("lang_and_sms_bulk_135")}</small> <code>{$data.spintax_sample.main}</code>
                    </p>
                    <p>
                        <small>{___(__("lang_form_literal_spintaxdesc2"), ["<strong>{$data.spintax_sample.good}</strong>", "<strong>{$data.spintax_sample.bad}</strong>"])}</small>
                    </p>
                </div>

                <div class="form-group col-md-6">
                    <div class="row">
                        <div class="col-md-5">
                            <label>
                                {__("lang_form_waquicksend_prioritylabel")} <i class="la la-info-circle" title="{__("lang_form_waquicksend_priorityhelp")}"></i>
                            </label>
                            <select name="priority" class="form-control">
                                <option value="1">{__("lang_form_yes")}</option>
                                <option value="2" selected>{__("lang_form_no")}</option>
                            </select>
                        </div>

                        <div class="col-md-7">
                            <label>
                                {__("lang_forms_whatsapp_messagetype")} <i class="la la-info-circle" title="{__("lang_forms_whatsapp_messagetypehelp")}"></i>
                            </label>
                            <select name="message_type" class="form-control" zender-wa-type>
                                <option value="text" selected>{__("lang_forms_whatsapp_typetext")}</option>
                                <option value="media">{__("lang_forms_whatsapp_typemedia")}</option>
                                <option value="document">{__("lang_forms_whatsapp_typedoc")}</option>
                            </select>
                        </div>
                    </div>

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
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-lg btn-primary">
                <i class="la la-telegram la-lg"></i> {__("lang_btn_send")}
            </button>
        </div>
    </div>
</form>