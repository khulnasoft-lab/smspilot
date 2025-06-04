<?php
/**
 * Framework Application Config
 * @package MVC Framework
 * @author Titan Systems <mail@titansystems.ph>
 */

ini_set("display_errors", 1);

get_configs();

define("configuration", [
	"root_controller" => false,
	"root_action" => false,
	"default_controller" => "default",
	"default_action" => "index",
	"error_handler_class" => "MVC_ErrorHandler"
]);

define("autoload", [
	"libraries" => [
		["PhoneNumber", "phone"],
		["Sanitize", "sanitize"],
		["Session", "session"],
		["PHPMailer", "mail"],
		["Smarty", "smarty"],
		["Header", "header"],
		["Guzzle", "guzzle"],
		["Upload", "upload"],
		["Sheets", "sheet"],
		["Cache", "cache"],
		["File", "file"],
		["SCSS", "scss"],
		["Hash", "hash"],
		["Slug", "slug"],
		["URI", "url"],
		["Lex", "lex"]
	],
	"models" => [
		["Api_Model", "api"],
		["Cron_Model", "cron"],
		["Table_Model", "table"],
		["Widget_Model", "widget"],
		["System_Model", "system"],
		["Gateway_Model", "gateway"]
	]
]);