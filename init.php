<?php

error_reporting(E_ALL);
ini_set('xdebug.var_display_max_depth', 5);
ini_set('xdebug.var_display_max_data', 2048);
date_default_timezone_set('Europe/Berlin');

include('framework/Finder.php');

function aDebug() {
    $var = func_get_args();
    $var = count($var) == 1 && isset($var[0]) ? $var[0] : $var;
    $bt=debug_backtrace();
    if(isset($bt[0]) && isset($bt[0]['file'])) echo "<code><pre style='background:yellow; color:red'><a href=\"txmt://open?url=file://{$bt[0]['file']}&line={$bt[0]['line']}\">{$bt[0]['file']} ({$bt[0]['line']})</a></pre></code>";
    var_dump($var);
    return true;
}

require('vendor/autoload.php');

spl_autoload_register(function($classname){
    $searchpattern = $classname . '.php';
    $searchpaths = array('project', 'modules', 'framework');
    while (empty($pathname) && ($searchpath = array_shift($searchpaths))) {
        $pathname = Finder::find($searchpattern, $searchpath);
    }
    if ($pathname) {
        include_once($pathname);
    }
});

session_start();
define('ENV_TYPE', 'dev');
define('PASS_SALT', 'k.jna5v(8&');
define('BASE_PATH', dirname(__FILE__));
define('BASE_URL', 'http://' . (isset($_SERVER["SERVER_NAME"]) ? $_SERVER["SERVER_NAME"] : 'localhost') . substr(BASE_PATH, strlen($_SERVER["DOCUMENT_ROOT"])) . '/');
set_include_path(get_include_path() . PATH_SEPARATOR . BASE_PATH);