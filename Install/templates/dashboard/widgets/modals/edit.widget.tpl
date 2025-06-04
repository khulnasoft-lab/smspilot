<form zender-form>
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="la la-puzzle-piece la-lg"></i> {$title}
            </h3>

            <button type="button" class="close" data-dismiss="modal">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        
        <div class="modal-body">
            <div class="form-row">
                <div class="form-group col-4">
                    <label>{lang_form_name}</label>
                    <input type="text" name="name" class="form-control" placeholder="eg. {lang_form_widgetname_placeholder}e" value="{$data.widget.name}">
                </div>

                <div class="form-group col-4">
                    <label>{lang_form_widgeticon}</label>
                    <small class="text-muted">
                        {lang_form_formodals}
                    </small>
                    <input type="text" name="icon" class="form-control" placeholder="eg. la la-info-circle" value="{$data.widget.icon}">
                </div>

                <div class="form-group col-4">
                    <label>{lang_form_widgettype}</label>
                    <select name="type" class="form-control">
                        <option value="1" {if $data.widget.type < 2}selected{/if}>Block</option>
                        <option value="2" {if $data.widget.type > 1}selected{/if}>Modal</option>
                    </select>
                </div>

                <div class="form-group col-6">
                    <label>Size</label>
                    <small class="text-muted">
                        {lang_form_formodals}
                    </small>
                    <select name="size" class="form-control">
                        <option value="sm" {if $data.widget.size eq "sm"}selected{/if}>{lang_form_widgetsmall}</option>
                        <option value="md" {if $data.widget.size eq "md"}selected{/if}>{lang_form_widgetmedium}</option>
                        <option value="lg" {if $data.widget.size eq "lg"}selected{/if}>{lang_form_widgetlarge}</option>
                        <option value="xl" {if $data.widget.size eq "xl"}selected{/if}>{lang_form_widgetxlarge}</option>
                    </select>
                </div>

                <div class="form-group col-6">
                    <label>{lang_form_widgetposition}</label>
                    <small class="text-muted">
                        {lang_form_formodals}
                    </small>
                    <select name="position" class="form-control">
                        <option value="center" {if $data.widget.position eq "center"}selected{/if}>{lang_form_widgetcenter}</option>
                        <option value="left" {if $data.widget.position eq "left"}selected{/if}>{lang_form_widgetleft}</option>
                        <option value="right" {if $data.widget.position eq "right"}selected{/if}>{lang_form_widgetright}</option>
                    </select>
                </div>

                <div class="form-group col-12">
                    <label>{lang_form_widgetcontent}</label>
                    <small class="text-danger">
                        {lang_form_widgetcontentdesc}
                    </small>

                    <div zender-codeflask>{$data.content}</div>
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