<?php
/**
 * Zender - Android Mobile Devices as SMS Gateway (SaaS Platform)
 * @author Titan Systems <mail@titansystems.ph>
 */ 

    /**
     * Error Reporting
     */
     
    error_reporting(E_ALL);

    /**
     * Start Session
     */

    session_start();

    /**
     * Installation Check
     */
    $env_file = __DIR__ . '/system/configurations/cc_env.inc';
    $needs_install = false;
    if (!file_exists($env_file)) {
        $needs_install = true;
    } else {
        $env = @file_get_contents($env_file);
        $required = ['dbhost<=>', 'dbport<=>', 'dbname<=>', 'dbuser<=>', 'dbpass<=>', 'systoken<=>', 'installed<=>true'];
        foreach ($required as $req) {
            if (strpos($env, $req) === false) {
                $needs_install = true;
                break;
            }
        }
    }
    if ($needs_install) {
        header('Location: install');
        exit;
    }

    /**
     * Vendors
     */
     
    require "vendor/autoload.php";

    /**
     * Framework
     */
     
    require "system/framework.php";