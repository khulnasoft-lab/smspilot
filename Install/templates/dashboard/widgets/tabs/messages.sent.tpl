<div class="card">
    <div class="card-header">
        <h4 class="card-title"><i class="la la-telegram"></i> {lang_dashboard_messages_tabsenttitle}</h4>

        <div class="float-right">
            <button class="btn btn-lg btn-primary" zender-toggle="zender.sms.quick">
                <i class="la la-telegram la-lg"></i>
                <span class="d-none d-sm-inline">{lang_dashboard_btn_smsquick}</span>
            </button>
            
            <button class="btn btn-lg btn-primary" zender-toggle="zender.sms.bulk">
                <i class="la la-mail-bulk la-lg"></i>
                <span class="d-none d-sm-inline">{lang_dashboard_btn_smsbulk}</span>
            </button>

            <button class="btn btn-lg btn-primary" zender-toggle="zender.sms.excel">
                <i class="la la-file-excel la-lg"></i>
                <span class="d-none d-sm-inline">{lang_btn_bulkexcel}</span>
            </button>

            <button class="btn btn-lg btn-primary" zender-toggle="zender.history.sent">
                <i class="la la-calendar la-lg"></i>
                <span class="d-none d-sm-inline">{lang_dashboard_btn_history}</span>
            </button>
        </div>
    </div>

    <div class="card-body">
        <div class="dt-responsive table-responsive">
            <table class="table table-striped" zender-table></table>
        </div>
    </div>
</div>