<div class="modal-content">
    <div class="modal-header">
        <h3 class="modal-title">
            <i class="la la-android la-lg"></i> {$title}
        </h3>

        <button type="button" class="close" data-dismiss="modal">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    
    <div class="modal-body">
        <p class="text-justify">{lang_form_adddevice_one}</p>

        <h5 class="text-uppercase">{lang_form_adddevice_two}</h5>
        <p class="pl-3 text-justify">{lang_form_adddevice_three}</p>
        <p class="text-center">
            <a href="{site_url}/uploads/builder/gateway.apk" class="btn btn-lg btn-primary">
                <i class="la la-android la-lg text-success"></i> {lang_btn_download}<br>
                <small class="text-muted">{lang_form_adddevice_eight}</small>
            </a>
        </p>

        <h5 class="text-uppercase">{lang_form_adddevice_four}</h5>
        <p class="pl-3 text-justify">
            {lang_form_adddevice_five}

            <div id="zender-qrcode">
                <script>zender.qrcode("{$data.hash}", 220, 220);</script>
            </div>
        </p>

        <h5 class="text-uppercase">{lang_form_adddevice_six}</h5>
        <p class="pl-3 text-justify">{lang_form_adddevice_seven}</p>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-lg btn-primary btn-block" data-dismiss="modal">
            <i class="la la-check-circle la-lg"></i> {lang_btn_done}
        </button>
    </div>
</div>