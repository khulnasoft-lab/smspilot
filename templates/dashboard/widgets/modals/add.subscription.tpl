<form zender-form>
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="la la-crown la-lg"></i> {$title}
            </h3>

            <button type="button" class="close" data-dismiss="modal">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        
        <div class="modal-body">
            <div class="form-row">
                <div class="form-group col-12">
                    <label>User</label>
                    <select name="user" class="form-control" data-live-search="true">
                        {foreach $data.users as $user}
                        <option value="{$user@key}" data-tokens="{$user.token}" data-subtext="{$user.email}">{$user.name}</option>
                        {/foreach}
                    </select>
                </div>

                <div class="form-group col-12">
                    <label>Package</label>
                    <select name="package" class="form-control" data-live-search="true">
                        {foreach $data.packages as $package}
                            {if $package.id > 1}
                            <option value="{$package@key}" data-tokens="{strtolower($package.name)}">{$package.name}</option>
                            {/if}
                        {/foreach}
                    </select>
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