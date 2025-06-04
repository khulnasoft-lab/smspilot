{if permission("manage_transactions")}
<div class="card">
    <div class="card-header border-0">
        <h2>
            <i class="la la-coins"></i> {lang_dashboard_admin_tabdefaultearningstitle} 
            <span class="badge badge-primary">{strtoupper(system_currency)}</span>
        </h2>
        <h4 class="text-success">{lang_dashboard_admin_tabdefaultearningslast}</h4>
    </div>

    <div class="card-body pt-0">
        <div class="embed-responsive">
            <iframe class="embed-responsive-item position-relative" smspilot-iframe="{site_url}/widget/chart/admin.earnings"></iframe>
        </div>
    </div>
</div>
{/if}

<div class="card">
    <div class="card-header border-0">
        <h2>
            <i class="la la-sms"></i> {lang_dashboard_admin_tabdefaultmessagestitle}
        </h2>
        <h4 class="text-success">{lang_dashboard_admin_tabdefaultmessageslast}</h4>
    </div>

    <div class="card-body pt-0">
        <div class="embed-responsive">
            <iframe class="embed-responsive-item position-relative" smspilot-iframe="{site_url}/widget/chart/admin.messages"></iframe>
        </div>
    </div>
</div>


{if permission("manage_users")}
<div class="card">
    <div class="card-header border-0">
        <h2>
            <i class="la la-users-cog"></i> {lang_dashboard_admin_tabdefaultuserstitle}
        </h2>
        <h4 class="text-success">{lang_dashboard_admin_tabdefaultuserslast}</h4>
    </div>

    <div class="card-body pt-0">
        <div class="embed-responsive">
            <iframe class="embed-responsive-item position-relative" smspilot-iframe="{site_url}/widget/chart/admin.users"></iframe>
        </div>
    </div>
</div>
{/if}