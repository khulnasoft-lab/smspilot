<?php
/**
 * Framework System Config
 * @package MVC Framework
 * @author Titan Systems <mail@titansystems.ph>
 */

date_default_timezone_set("UTC");
define("base_dir", "system/");
define("error_handler", 1);

/**
 * Project Configs
 */

define("site_url", "//" . env["siteurl"] . env["port"] . env["subdir"]);
define("titansys_api", "https://api.titansystems.xyz");
define("titansys_builder", "https://builder.titansystems.xyz");
define("system_token", env["systoken"]);
