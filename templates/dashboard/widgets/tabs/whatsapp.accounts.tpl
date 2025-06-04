<div class="card">
    <div class="card-header">
        <h4 class="card-title"><i class="la la-whatsapp"></i> {__("lang_tabs_whatsappaccounts_titleheader")}</h4>

        <div class="float-right">
            <button class="btn btn-lg btn-primary" title="{__("lang_table_btn_refresh")}" zender-action="refresh">
                <i class="la la-refresh la-lg"></i>
            </button>

            <button class="btn btn-lg btn-primary" title="{__("lang_and_what_accnt_6")}" relink-unique="none" wa-link-url="link" wa-link-title="{__("lang_widget_waaddaccount_title")}" zender-toggle="zender.add.whatsapp">
                <i class="la la-whatsapp la-lg"></i>
                <span class="d-none d-sm-inline">{__("lang_and_what_accnt_8")}</span>
            </button>
        </div>
    </div>

    <div class="card-body">
        <div class="dt-responsive table-responsive">
            <table class="table table-striped" zender-table></table>
        </div>
    </div>
</div>