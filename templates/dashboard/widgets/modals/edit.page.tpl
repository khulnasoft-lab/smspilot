<form smspilot-form>
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="la la-stream la-lg"></i> {$title}
            </h3>

            <button type="button" class="close" data-dismiss="modal">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        
        <div class="modal-body">
            <div class="form-row">
                <div class="form-group col-6">
                    <label>{lang_form_name}</label>
                    <input type="text" name="name" class="form-control" placeholder="eg. {lang_form_widgetname_placeholder}" value="{$data.page.name}">
                </div>

                <div class="form-group col-3">
                    <label>{lang_form_pageroles}</label>
                    <select name="roles[]" class="form-control" multiple>
                        {foreach $data.roles as $role}
                        <option value="{$role@key}" {if $role.selected}selected{/if}>{$role.name}</option>
                        {/foreach}
                    </select>
                </div>

                <div class="form-group col-3">
                    <label>{lang_form_require_login}</label>
                    <select name="logged" class="form-control">
                        <option value="1" {if $data.page.logged < 2}selected{/if}>{lang_form_yes}</option>
                        <option value="2" {if $data.page.logged > 1}selected{/if}>{lang_form_no}</option>
                    </select>
                </div>

                <div class="form-group col-12">
                    <label>{lang_form_widgetcontent}</label>
                    <small class="text-danger">
                        {lang_form_widgetcontentdesc}
                    </small>

                    <div smspilot-codeflask>{$data.page.content}</div>
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