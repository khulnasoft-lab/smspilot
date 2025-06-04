<form zender-form>
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="la la-wrench la-lg"></i> {$title}
            </h3>

            <button type="button" class="close" data-dismiss="modal">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        
        <div class="modal-body">
            <div class="form-row">
                <div class="form-group col-12">
                    <label>{lang_form_name}</label>
                    <input type="text" name="name" class="form-control" placeholder="eg. {lang_form_templatename_placeholder}">
                </div>

                <div class="form-group col-12">
                    <label>{lang_form_templateformat}</label>
                    <textarea name="format" class="form-control" rows="5" placeholder="{lang_form_templateformat_placeholder}"></textarea>
                </div>

                <div class="form-group col-12">
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

        <div class="modal-footer">
            <button type="submit" class="btn btn-lg btn-primary btn-block">
                <i class="la la-check-circle la-lg"></i> {lang_btn_submit}
            </button>
        </div>
    </div>
</form>