<form zender-form>
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="la la-address-book la-lg"></i> {$title}
            </h3>

            <button type="button" class="close" data-dismiss="modal">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        
        <div class="modal-body">
            <div class="form-row">
                <div class="form-group col-6">
                    <label>{lang_form_name}</label>
                    <input type="text" name="name" class="form-control" placeholder="eg. Korean" value="{$data.language.name}">
                </div>

                <div class="form-group col-6">
                    <label>{lang_form_countrycode}</label>
                    <select name="iso" class="form-control" data-live-search="true">
                        {foreach $data.countries as $country}
                        <option value="{$country@key}" data-tokens="{strtolower($country)}" {if $country@key eq $data.language.iso}selected{/if}>{$country@key}</option>
                        {/foreach}
                    </select>
                </div>
                
                <div class="form-group col-12">
                    <label>{lang_form_translations}</label>
                    <textarea name="translations" class="form-control" cols="100" rows="10" placeholder="{lang_form_translations_placeholder}">{$data.language.translations}</textarea>
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