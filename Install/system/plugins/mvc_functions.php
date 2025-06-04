<?php
/**
 * @desc Helper functions
 */

function get_configs()
{
    if(!file_exists("system/configurations/cc_env.inc"))
        die("Environment config not found!");

    $configs = explode("\n", file_get_contents("system/configurations/cc_env.inc"));
    foreach($configs AS $config):
        $line = explode("<=>", $config);
        $vals[$line[0]] = (isset($line[1]) ? $line[1] : false);
    endforeach;

    $vals["siteurl"] = (isset($_SERVER["SERVER_NAME"]) ? $_SERVER["SERVER_NAME"] : php_uname("n"));
    $vals["port"] = (in_array($_SERVER["SERVER_PORT"], [80, 443]) ? false : ":{$_SERVER["SERVER_PORT"]}");
    $vals["subdir"] = (empty(explode("/", dirname($_SERVER["SCRIPT_NAME"]))[1]) ? false : dirname($_SERVER["SCRIPT_NAME"]));

    define("env", $vals);

    if(!isset(env["installed"]) && !Stringy\create($_SERVER["REQUEST_URI"])->contains("install"))
        header("location: ./install");

    return get_version();
}

function get_version()
{
    if(!file_exists("system/configurations/cc_ver.inc"))
        die("Version config not found!");

    return define("version", file_get_contents("system/configurations/cc_ver.inc"));
}

function site_url($path = false)
{
    return site_url . "/" . rtrim($path, "/") . "?" . \Volnix\CSRF\CSRF::getQueryString("_token");
}

function response($status, $msg, $data = false)
{
    return die(json_encode([
        "status" => $status, 
        "message" => $msg, 
        "data" => $data
    ]));
}

function set_template($name)
{
    return define("template", $name);
}

function set_language($translations)
{
    $keys = [
        "landing_title_default",
        "landing_nav_features",
        "landing_nav_pricing",
        "landing_nav_clients",
        "landing_nav_api",
        "landing_nav_login",
        "landing_lead_head",
        "landing_lead_desc",
        "landing_lead_btn",
        "landing_feat_title",
        "landing_feat_titledesc",
        "landing_feat_onesub",
        "landing_feat_onetitle",
        "landing_feat_onedesc",
        "landing_feat_twosub",
        "landing_feat_twotitle",
        "landing_feat_twodesc",
        "landing_feat_threesub",
        "landing_feat_threetitle",
        "landing_feat_threedesc",
        "landing_pricing_title",
        "landing_pricing_desc",
        "landing_pricing_send",
        "landing_pricing_receive",
        "landing_pricing_devices",
        "landing_pricing_keys",
        "landing_pricing_hooks",
        "landing_pricing_month",
        "landing_pricing_contact",
        "landing_footer_ourcomp",
        "landing_footer_about",
        "landing_footer_tos",
        "landing_footer_privacy",
        "landing_footer_links",
        "landing_footer_contact",
        "landing_footer_login",
        "landing_footer_register",
        "landing_footer_startfree",
        "landing_footer_startdesc",
        "landing_footer_copyright",
        "dashboard_title_default",
        "dashboard_title_messages",
        "dashboard_title_contacts",
        "dashboard_title_devices",
        "dashboard_title_tools",
        "dashboard_title_admin",
        "dashboard_nav_default",
        "dashboard_nav_messages",
        "dashboard_nav_contacts",
        "dashboard_nav_devices",
        "dashboard_nav_tools",
        "dashboard_nav_admin",
        "dashboard_nav_menusubscription",
        "dashboard_nav_menusettings",
        "dashboard_nav_menulogout",
        "dashboard_btn_smsquick",
        "dashboard_btn_smsbulk",
        "dashboard_btn_addcontact",
        "dashboard_btn_adddevice",
        "dashboard_btn_addgroup",
        "dashboard_btn_history",
        "dashboard_btn_addtemplate",
        "dashboard_btn_addkey",
        "dashboard_btn_addhook",
        "dashboard_btn_forums",
        "dashboard_btn_build",
        "dashboard_btn_buildsettings",
        "dashboard_btn_settings",
        "dashboard_btn_theme",
        "dashboard_btn_adduser",
        "dashboard_btn_addpackage",
        "dashboard_btn_addwidget",
        "dashboard_btn_addlanguage",
        "dashboard_footer_about",
        "dashboard_footer_terms",
        "dashboard_footer_privacy",
        "dashboard_footer_copyright",
        "dashboard_default_title",
        "dashboard_default_summarytitle",
        "dashboard_default_summarysubtitle",
        "dashboard_default_summarysent",
        "dashboard_default_summaryreceived",
        "dashboard_default_summaryrequests",
        "dashboard_default_summaryhooks",
        "dashboard_default_summarykeys",
        "dashboard_default_packagetitle",
        "dashboard_default_packagesend",
        "dashboard_default_packagereceive",
        "dashboard_default_packagedevice",
        "dashboard_default_package",
        "dashboard_default_lastsent",
        "dashboard_default_lastreceived",
        "dashboard_default_viewall",
        "dashboard_default_nothinghere",
        "dashboard_messages_title",
        "dashboard_messages_menusent",
        "dashboard_messages_menureceived",
        "dashboard_messages_menutemplates",
        "dashboard_messages_tabsenttitle",
        "dashboard_messages_tabreceivedtitle",
        "dashboard_messages_tabtemplatestitle",
        "dashboard_messages_tablesentrecipient",
        "dashboard_messages_tablesentmessage",
        "dashboard_messages_tablesentdevice",
        "dashboard_messages_tablesentcreated",
        "dashboard_messages_tablesentdetails",
        "dashboard_messages_tablesentoptions",
        "dashboard_messages_tablereceivedsender",
        "dashboard_messages_tablereceivedmessage",
        "dashboard_messages_tablereceiveddevice",
        "dashboard_messages_tablereceivedreceived",
        "dashboard_messages_tablereceivedoptions",
        "dashboard_messages_tabletemplatesname",
        "dashboard_messages_tabletemplatesformat",
        "dashboard_messages_tabletemplatesoptions",
        "dashboard_contacts_title",
        "dashboard_contacts_menusaved",
        "dashboard_contacts_menugroups",
        "dashboard_contacts_tabsavedtitle",
        "dashboard_contacts_tabgroupstitle",
        "dashboard_contacts_tablesavedname",
        "dashboard_contacts_tablesavednumber",
        "dashboard_contacts_tablesavedgroup",
        "dashboard_contacts_tablesavedoptions",
        "dashboard_contacts_tablegroupscontacts",
        "dashboard_contacts_tablegroupsname",
        "dashboard_contacts_tablegroupsoptions",
        "dashboard_devices_title",
        "dashboard_devices_menuregistered",
        "dashboard_devices_menuguide",
        "dashboard_devices_tabregisteredtitle",
        "dashboard_devices_tabguidetitle",
        "dashboard_devices_tableregisteredmodel",
        "dashboard_devices_tableregisteredbrand",
        "dashboard_devices_tableregisteredversion",
        "dashboard_devices_tableregisteredadded",
        "dashboard_devices_tableregisteredoptions",
        "dashboard_tools_title",
        "dashboard_tools_menukeys",
        "dashboard_tools_menuhooks",
        "dashboard_tools_menuapidoc",
        "dashboard_tools_menuhookdoc",
        "dashboard_tools_tabkeystitle",
        "dashboard_tools_tabhookstitle",
        "dashboard_tools_tabapidoctitle",
        "dashboard_tools_tabhookdoctitle",
        "dashboard_tools_tablekeysname",
        "dashboard_tools_tablekeysdevices",
        "dashboard_tools_tablekeyspermissions",
        "dashboard_tools_tablekeyscreated",
        "dashboard_tools_tablekeysoptions",
        "dashboard_tools_tablehooksname",
        "dashboard_tools_tablehooksurl",
        "dashboard_tools_tablehooksdevices",
        "dashboard_tools_tablehookscreated",
        "dashboard_tools_tablehooksoptions",
        "dashboard_admin_title",
        "dashboard_admin_menustats",
        "dashboard_admin_menuusers",
        "dashboard_admin_menupackages",
        "dashboard_admin_menusubscriptions",
        "dashboard_admin_menutransactions",
        "dashboard_admin_menuwidgets",
        "dashboard_admin_menulanguages",
        "dashboard_admin_tabdefaultearningstitle",
        "dashboard_admin_tabdefaultearningsusd",
        "dashboard_admin_tabdefaultearningslast",
        "dashboard_admin_tabdefaultmessagestitle",
        "dashboard_admin_tabdefaultmessageslast",
        "dashboard_admin_tabdefaultuserstitle",
        "dashboard_admin_tabdefaultuserslast",
        "dashboard_admin_tabuserstitle",
        "dashboard_admin_tabpackagestitle",
        "dashboard_admin_tabsubscriptionstitle",
        "dashboard_admin_tabtransactionstitle",
        "dashboard_admin_tabwidgetstitle",
        "dashboard_admin_tablanguagestitle",
        "dashboard_admin_tableusersname",
        "dashboard_admin_tableusersemail",
        "dashboard_admin_tableuserslanguage",
        "dashboard_admin_tableusersjoin",
        "dashboard_admin_tableusersoptions",
        "dashboard_admin_tablepackagesname",
        "dashboard_admin_tablepackagesprice",
        "dashboard_admin_tablepackagessend",
        "dashboard_admin_tablepackagesreceive",
        "dashboard_admin_tablepackagesdevices",
        "dashboard_admin_tablepackageskeys",
        "dashboard_admin_tablepackageshooks",
        "dashboard_admin_tablepackagesoptions",
        "dashboard_admin_tablesubscriptionsuser",
        "dashboard_admin_tablesubscriptionspackage",
        "dashboard_admin_tablesubscriptionsprice",
        "dashboard_admin_tablesubscriptionsstart",
        "dashboard_admin_tablesubscriptionsexpire",
        "dashboard_admin_tablesubscriptionsoptions",
        "dashboard_admin_tabletransactionscustomer",
        "dashboard_admin_tabletransactionspackage",
        "dashboard_admin_tabletransactionsamount",
        "dashboard_admin_tabletransactionsprovider",
        "dashboard_admin_tabletransactionsdate",
        "dashboard_admin_tablewidgetsname",
        "dashboard_admin_tablewidgetstype",
        "dashboard_admin_tablewidgetssize",
        "dashboard_admin_tablewidgetsposition",
        "dashboard_admin_tablewidgetscreated",
        "dashboard_admin_tablewidgetsoptions",
        "dashboard_admin_tablelanguagesiso",
        "dashboard_admin_tablelanguagesname",
        "dashboard_admin_tablelanguagessize",
        "dashboard_admin_tablelanguagescreated",
        "dashboard_admin_tablelanguagesoptions",
        "dashboard_admin_statssenttitle",
        "dashboard_admin_statsreceivedtitle",
        "dashboard_admin_statsregisteredtitle",
        "table_search_text",
        "table_search_placeholder",
        "response_invalid",
        "response_went_wrong",
        "response_session_false",
        "response_session_true",
        "response_invalid_emailpass",
        "response_loggedin_success",
        "response_loggedout_success",
        "response_invalid_email",
        "response_retrieval_received",
        "response_retrieval_sent",
        "response_solve_captcha",
        "response_register_false",
        "response_name_short",
        "response_password_short",
        "response_password_notmatch",
        "response_register_success",
        "response_email_unavailable",
        "response_upload_applogo",
        "response_upload_appsplash",
        "response_pcode_empty",
        "response_buildserver_false",
        "response_invalid_daterange",
        "response_date_maxrange",
        "response_found_senthistory",
        "response_notfound_senthistory",
        "response_found_receivehistory",
        "response_notfound_receivehistory",
        "response_invalid_number",
        "response_message_short",
        "response_message_queued",
        "response_message_bulkqueued",
        "response_format_short",
        "response_template_added",
        "response_number_exist",
        "response_contact_added",
        "response_group_added",
        "response_permission_min",
        "response_key_added",
        "response_invalid_webhookurl",
        "response_webhook_added",
        "response_user_added",
        "response_package_priceinvalid",
        "response_package_sendinvalid",
        "response_package_receiveinvalid",
        "response_package_deviceinvalid",
        "response_package_keyinvalid",
        "response_package_hookinvalid",
        "response_package_added",
        "response_widget_added",
        "response_language_added",
        "response_avatar_invalid",
        "response_profile_updated",
        "response_invalid_cardnumber",
        "response_invalid_cardexpiry",
        "response_package_purchasedtitle",
        "response_package_purchased",
        "response_card_decline",
        "response_template_updated",
        "response_contact_updated",
        "response_group_updated",
        "response_key_updated",
        "response_webhook_updated",
        "response_user_updated",
        "response_package_premiumfalse",
        "response_package_updated",
        "response_widget_updated",
        "response_language_updated",
        "response_builder_packagenameshort",
        "response_builder_appnameshort",
        "response_builder_invalidsend",
        "response_builder_invalidreceive",
        "response_builder_applogofail",
        "response_builder_appsplashfail",
        "response_builder_settingsupdated",
        "response_theme_updated",
        "response_system_settingsupdated",
        "response_deleted_sent",
        "response_deleted_received",
        "response_deleted_template",
        "response_deleted_contact",
        "response_deleted_group",
        "response_deleted_device",
        "response_deleted_key",
        "response_deleted_hook",
        "response_deleted_defaultuserfalse",
        "response_deleted_user",
        "response_deleted_defaultpackagefalse",
        "response_deleted_package",
        "response_deleted_subscription",
        "response_deleted_widget",
        "response_deleted_defaultlangfalse",
        "response_deleted_language",
        "response_lang_changed",
        "modal_login_title",
        "modal_forgot_title",
        "modal_register_title",
        "modal_apiguide_title",
        "modal_usersettings_title",
        "modal_subscription_title",
        "modal_purchase_title",
        "modal_packages_title",
        "modal_smsquick_title",
        "modal_smsbulk_title",
        "modal_findsent_title",
        "modal_findreceived_title",
        "modal_addtemplate_title",
        "modal_edittemplate_title",
        "modal_addcontact_title",
        "modal_editcontact_title",
        "modal_addgroup_title",
        "modal_editgroup_title",
        "modal_adddevice_title",
        "modal_addkey_title",
        "modal_editkey_title",
        "modal_addhook_title",
        "modal_edithook_title",
        "modal_buildersettings_title",
        "modal_themesettings_title",
        "modal_systemsettings_title",
        "modal_adduser_title",
        "modal_edituser_title",
        "modal_addpackage_title",
        "modal_editpackage_title",
        "modal_addwidget_title",
        "modal_editwidget_title",
        "modal_addlanguage_title",
        "modal_editlanguage_title",
        "require_email",
        "require_password",
        "require_name",
        "require_cpassword",
        "require_cardnumber",
        "require_cardexpiry",
        "require_cardname",
        "require_cardcvc",
        "require_phone",
        "require_device",
        "require_message",
        "require_groups",
        "require_sim",
        "require_priority",
        "require_date",
        "require_api",
        "require_templatename",
        "require_templateformat",
        "require_contactname",
        "require_group",
        "require_groupname",
        "require_apiname",
        "require_devices",
        "require_permissions",
        "require_hookname",
        "require_hookurl",
        "require_appname",
        "require_appcolor",
        "require_appsend",
        "require_appreceive",
        "require_builderemail",
        "require_packagename",
        "require_packageprice",
        "require_packagesend",
        "require_packagereceive",
        "require_packagedevice",
        "require_packagekey",
        "require_packagehook",
        "require_widgetname",
        "require_widgetsize",
        "require_widgetposition",
        "require_widgettype",
        "require_languagename",
        "require_languageiso",
        "require_languagestr",
        "btn_submit",
        "btn_download",
        "btn_done",
        "btn_search",
        "btn_purchase",
        "btn_free",
        "btn_send",
        "btn_save",
        "btn_packages",
        "btn_retrieve",
        "btn_signin",
        "btn_signup",
        "form_name",
        "form_number",
        "form_group",
        "form_adddevice_one",
        "form_adddevice_two",
        "form_adddevice_three",
        "form_adddevice_four",
        "form_adddevice_five",
        "form_adddevice_six",
        "form_adddevice_seven",
        "form_adddevice_eight",
        "form_devices",
        "form_permissions",
        "form_countrycode",
        "form_translations",
        "form_translations_placeholder",
        "form_packageprice",
        "form_packagesend",
        "form_packagereceive",
        "form_inusd",
        "form_perday",
        "form_packagedevice",
        "form_packagekey",
        "form_packagehook",
        "form_packagereminder",
        "form_packageremindermsg",
        "form_templatename_placeholder",
        "form_templateformat",
        "form_templateformat_placeholder",
        "form_shortcodes",
        "form_emailaddress",
        "form_password",
        "form_password_leave",
        "form_language",
        "form_webhookname_placeholder",
        "form_webhookurl",
        "form_automatic",
        "form_widgetname_placeholder",
        "form_widgeticon",
        "form_formodals",
        "form_widgettype",
        "form_widgetsize",
        "form_widgetsmall",
        "form_widgetmedium",
        "form_widgetlarge",
        "form_widgetxlarge",
        "form_widgetposition",
        "form_widgetcenter",
        "form_widgetleft",
        "form_widgetright",
        "form_widgetcontent",
        "form_widgetcontentdesc",
        "form_builderalert",
        "form_builderpackagename",
        "form_builderpackagename_unique",
        "form_builderappname",
        "form_required",
        "form_builderappdesc",
        "form_builderappdesc_placeholder",
        "form_builderappcolor",
        "form_optional",
        "form_buildershouldmatch",
        "form_buildersend",
        "form_buildersend_sec",
        "form_builderreceive",
        "form_builderreceive_sec",
        "form_builderemail",
        "form_builderemail_small",
        "form_builderapplogo",
        "form_builderapplogo_logoimg",
        "form_builderappsplash",
        "form_builderappsplash_splashimg",
        "form_uploaded",
        "form_notuploaded",
        "form_themebg",
        "form_themetext",
        "form_settingsite",
        "form_settingssitename",
        "form_settingssitedesc",
        "form_settingspcode",
        "form_settingsprotocol",
        "form_settingsdeflang",
        "form_settingsreg",
        "form_settingsmailing",
        "form_settingsmailfunc",
        "form_settingssitemail",
        "form_settingssmtphost",
        "form_settingssmtpport",
        "form_settingssmtpusername",
        "form_settingssmtppassword",
        "form_settingssmtp_small",
        "form_settingspayments",
        "form_settingspaypalusername",
        "form_settingspaypalpassword",
        "form_settingspaypalsignat",
        "form_settingsstripesecret",
        "form_settingspaypaltest",
        "form_settingssecurity",
        "form_settingsrecaptchakey",
        "form_settingsrecaptchasecret",
        "form_enable",
        "form_disable",
        "form_native",
        "form_remotesmtp",
        "form_daterange",
        "form_device",
        "form_alldevices",
        "form_sim",
        "form_priority",
        "form_yes",
        "form_no",
        "form_all",
        "form_apisent",
        "form_cardnumber",
        "form_cardexpiry",
        "form_cardname",
        "form_cardcvc",
        "form_provider",
        "form_freefor",
        "form_monthly",
        "form_dailysend",
        "form_dailyreceive",
        "form_alloweddevices",
        "form_allowedkeys",
        "form_allowedhooks",
        "form_groups",
        "form_template",
        "form_none",
        "form_message",
        "form_message_placeholder",
        "form_avatar",
        "form_changepass",
        "form_welcome",
        "form_package",
        "form_send",
        "form_receive",
        "form_subdevices",
        "form_keys",
        "form_hooks",
        "form_daily",
        "form_alreadyremember",
        "form_forgotpass",
        "form_fullname",
        "form_cpassword",
        "form_haveaccount",
        "datatable_processing",
        "datatable_length",
        "datatable_info",
        "datatable_empty",
        "datatable_filtered",
        "datatable_loading",
        "datatable_zero",
        "datatable_null",
        "datatable_first",
        "datatable_prev",
        "datatable_next",
        "datatable_last",
        "delete_title",
        "delete_tagline",
        "validate_cannotemp",
        "alert_attention",
        "copy_data",
        "date_today",
        "date_yesterday",
        "date_7days",
        "date_30days",
        "date_month",
        "date_lmonth",
        "date_custom",
        "api_response_invalcode",
        "api_response_limitext",
        "api_response_limitreg",
        "api_response_buildsuccess",
        "api_response_forumtrue",
        "devices_guide_line1",
        "devices_guide_line2",
        "devices_guide_line3",
        "devices_guide_line4",
        "devices_guide_line5",
        "devices_guide_line6",
        "tools_webhookguide_line1",
        "tools_webhookguide_line2",
        "tools_webhookguide_line3",
        "tools_webhookguide_line4",
        "tools_webhookguide_line5",
        "tools_webhookguide_line6",
        "### Version 1.1",
        "payment_proccess_error",
        "payment_failed",
        "unknown_action_method",
        "mollie_redirecting_page",
        "form_settingsstripekey",
        "form_settingsmolliekey",
        "btn_confirm",
        "btn_cancel",
        "recaptcha_add_keys",
        "pay_with_paypal",
        "pay_with_stripe",
        "pay_with_mollie",
        "payment_provider",
        "form_settingsenabledproviders",
        "form_builderappicon",
        "form_builderappicon_iconimg",
        "payment_transact_failed",
        "cookieconsent_message",
        "cookieconsent_link",
        "role_added",
        "role_updated",
        "role_deleted",
        "role_default_update",
        "role_default_delete",
        "btn_close",
        "voucher_added",
        "pay_with_voucher",
        "form_voucher",
        "btn_redeem",
        "invalid_voucher_code",
        "voucher_code_unmatched",
        "voucher_deleted",
        "form_title_addvoucher",
        "form_title_addsubscription",
        "form_adduser_role",
        "require_pagename",
        "require_pageroles",
        "form_pageroles",
        "require_vouchercode",
        "form_settingscurrency",
        "btn_bulkexcel",
        "response_leastprovider",
        "packages_landingsavedcontacts",
        "packages_dashboardallowedcontacts",
        "response_noregistereddevices",
        "mail_subscriptionexpired",
        "user_subscriptioncontacts",
        "tooltips_viewfirst",
        "tooltips_viewsecond",
        "tooltips_viewthird",
        "tooltips_viewfourth",
        "response_suspended",
        "response_adminsuspend",
        "response_successsuspend",
        "response_successunsuspend",
        "suspend_user_title",
        "suspend_user_desc",
        "unsuspend_user_title",
        "unsuspend_user_desc",
        "all_languages",
        "response_invalid_excel",
        "response_contacts_imported",
        "table_role_name",
        "table_role_permissions",
        "table_voucher_name",
        "table_voucher_package",
        "table_voucher_created",
        "table_page_name",
        "table_page_require",
        "table_page_roles",
        "table_page_created",
        "import_btn",
        "form_excelfile",
        "form_followformat",
        "form_clickhere",
        "alert_impersonate_title",
        "alert_impersonate_desc",
        "alert_impersonateexit_title",
        "alert_impersonateexit_desc",
        "form_supportchat",
        "impersonate_exit_header",
        "form_hook_addtitle",
        "form_hook_edittitle",
        "form_hook_event",
        "form_hook_link",
        "form_autoreply_addtitle",
        "form_autoreply_edittitle",
        "form_autoreply_keywords",
        "form_autoreply_message",
        "require_action_name",
        "require_action_event",
        "require_action_link",
        "require_action_keywords",
        "require_action_message",
        "response_invalid_linkstructure",
        "response_hook_added",
        "response_invalid_keywords",
        "response_autoreply_added",
        "response_hook_updated",
        "response_autoreply_updated",
        "response_action_deleted",
        "table_action_name",
        "table_action_type",
        "table_action_event",
        "table_action_devices",
        "form_scheduled_title",
        "response_scheduled_deleted",
        "response_no_permission",
        "response_builder_appiconfail",
        "form_require_login",
        "widget_addpage_title",
        "widget_editpage_title",
        "require_subscription_user",
        "require_subscription_package",
        "require_voucher_name",
        "require_voucher_package",
        "widget_alllang_title",
        "widget_smsexcel_title",
        "require_scheduled_name",
        "require_scheduled_date",
        "widget_importcontacts_title",
        "widget_addrole_title",
        "require_addrole_name",
        "require_addrole_permissions",
        "widget_editrole_title",
        "response_page_added",
        "response_page_updated",
        "response_page_deleted",
        "table_scheduled_name",
        "table_scheduled_recipients",
        "table_scheduled_repeat",
        "table_scheduled_schedule",
        "messages_scheduled_title",
        "messages_scheduled_schedule",
        "form_schedule_numbers",
        "form_schedule_schedule",
        "form_schedule_repeat",
        "form_bulksms_numbers",
        "dashboard_messages_menuscheduled",
        "dashboard_tools_menuactions",
        "tools_actions_title",
        "tools_btn_addhook",
        "tools_btn_addautoreply",
        "dashboard_admin_menuroles",
        "dashboard_admin_menuvouchers",
        "dashboard_admin_menupages",
        "admin_gateway_title",
        "admin_gateway_status",
        "admin_gateway_notuploaded",
        "admin_gateway_uploaded",
        "dashboard_roles_title",
        "dashboard_roles_addrole",
        "dashboard_vouchers_title",
        "dashboard_vouchers_addrole",
        "form_roles_manageusers",
        "form_roles_managepackages",
        "form_roles_managevouchers",
        "form_roles_managesubscriptions",
        "form_roles_managetransactions",
        "form_roles_managewidgets",
        "form_roles_managepages",
        "form_roles_managelanguages",
        "form_roles_managefields",
        "form_voucher_package",
        "dashboard_pages_title",
        "dashboard_pages_addpage",
        "response_package_contactinvalid",
        "form_theme_landlogo",
        "form_theme_dashlogo",
        "form_theme_favicon",
        "form_package_contactslimit",
        "response_limitation_send",
        "response_limitation_contact",
        "response_limitation_key",
        "response_limitation_webhook",
        "form_adminsettings_token",
        "api_response_buildwait",
        "response_upload_appicon",
        "app_status_gateway_running",
        "app_status_gateway_touch",
        "app_device_registered",
        "app_device_unregistered",
        "app_terminal_gateway_ready",
        "app_terminal_gateway_register",
        "app_terminal_gateway_hash",
        "app_terminal_gateway_registered",
        "app_terminal_gateway_device",
        "app_terminal_gateway_connecterror",
        "app_terminal_gateway_started",
        "app_terminal_gateway_stopped",
        "app_terminal_gateway_unregistered",
        "app_terminal_uid_failed",
        "app_terminal_feature_error",
        "app_terminal_connection_restored",
        "app_terminal_gateway_errorstop",
        "app_terminal_gateway_cantconnect",
        "app_terminal_sms_sent",
        "app_terminal_message_failed",
        "app_terminal_device_error",
        "app_dialog_wait",
        "app_dialog_exit",
        "app_dialog_exit_desc",
        "app_camera_qrcode_inside",
        "app_ui_status",
        "app_ui_exit"
    ];

    $lines = explode("\n", trim($translations));
    
    foreach($lines as $line):
        if(Stringy\create($line)->contains("===")):
            $columns = explode("===", trim($line));
            $lkeys[] = trim($columns[0]);
            $lvalues[trim($columns[0])] = trim($columns[1]);
        endif;
    endforeach;

    foreach($keys as $key):
        if(in_array($key, $lkeys)):
            define("lang_{$key}", $lvalues[$key]);
        else:
            define("lang_{$key}", "lang_{$key}");
        endif;
    endforeach;
}

function set_blocks($blocks)
{
    foreach($blocks as $key => $value):
        define("block_{$key}", $value);
    endforeach;
}

function set_logged($user)
{
    if(empty($user)):
        $user = [
            "id" => false,
            "admin" => false,
            "hash" => false,
            "email" => false,
            "name" => false,
            "permissions" => false
        ];
    endif;

    $user["language"] = (isset($_SESSION["language"]) ? $_SESSION["language"] : (isset($_SESSION["logged"]["language"]) ? $_SESSION["logged"]["language"] : system_default_lang));

    $permissions = [
        "manage_users",
        "manage_packages",
        "manage_vouchers",
        "manage_subscriptions",
        "manage_transactions",
        "manage_widgets",
        "manage_pages",
        "manage_languages",
        "manage_fields"
    ];

    $user["permissions"] = explode(",", $user["permissions"]);

    if($user["id"] < 2)
        define("super_admin", true);
    else
        define("super_admin", false);

    if(!empty($user["permissions"][0]) || $user["id"] < 2):
        define("is_admin", true);
    else:
        define("is_admin", false);
    endif;

    foreach($permissions as $permission):
        if(in_array($permission, $user["permissions"])):
            define("perm_{$permission}", true);
        else:
            define("perm_{$permission}", false);
        endif;
    endforeach;

    foreach($user as $key => $value):
        define("logged_{$key}", $value);
    endforeach;

    return define("avatar", (file_exists("uploads/avatars/" . $user["hash"] . ".jpg") ? site_url . "/uploads/avatars/" . $user["hash"] . ".jpg?v=" . filemtime("uploads/avatars/" . $user["hash"] . ".jpg") : site_url . "/uploads/avatars/noavatar.png"));
}

function set_system($system)
{
    foreach($system as $key => $value):
        define("system_{$key}", $value);
    endforeach;
}

function set_subscription($subscription)
{
    foreach($subscription as $key => $value):
        define("subscription_{$key}", $value);
    endforeach;
}

function permission($permission)
{
    if(logged_id < 2):
        return true;
    else:
        if(constant("perm_{$permission}"))
            return true;
        else
            return false;
    endif;
}

function limitation($limit, $used)
{
    return $used >= $limit ? true : false;
}

function _block($id)
{
    return defined("block_{$id}") ? constant("block_{$id}") : false;
}

function _assets($path)
{
    return (Stringy\create($path)->contains(".js") || Stringy\create($path)->contains(".css") ? site_url . "/templates/_assets/{$path}?v=" . md5(version) : site_url . "/templates/_assets/{$path}");
}

function assets($path, $template = template)
{
    return (Stringy\create($path)->contains(".js") || Stringy\create($path)->contains(".css") ? site_url . "/templates/{$template}/assets/{$path}?v=" . md5(filemtime("templates/{$template}/assets/{$path}")) : site_url . "/templates/{$template}/assets/{$path}");
}

function logo($type, $block = false)
{
    if($block):
        if($type == "landing"):
            return file_exists("uploads/theme/landing.png") ? "<img src=\"" . site_url("uploads/theme/landing.png") . "\">" : $block;
        else:
            return file_exists("uploads/theme/dashboard.png") ? "<img src=\"" . site_url("uploads/theme/dashboard.png") . "\">" : $block;
        endif;
    else:
        return file_exists("uploads/theme/favicon.png") ? site_url("uploads/theme/favicon.png") : _assets("images/favicon.png");
    endif;
}

function sign()
{
    $signs = [
        "usd" => "$",
        "eur" => "€",
        "gbp" => "£",
        "aud" => "AU$",
        "cad" => "CA$",
        "hkd" => "HK$",
        "jpy" => "¥",
        "rub" => "₽",
        "sgd" => "S$"
    ];

    return $signs[system_currency];
}

function count_months($start, $end)
{
    $month = 1;
    $min = min(strtotime($start), strtotime($end));
    $max = max(strtotime($start), strtotime($end));

    while(($min = strtotime("+1 month", $min)) <= $max):
        $month++;
    endwhile;

    return $month;
}

function truncate($array, $max = 10)
{
    krsort($array);
    return array_slice($array, 0, $max, true);
}