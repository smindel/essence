<?php

ini_set('xdebug.var_display_max_depth', 5);
ini_set('xdebug.var_display_max_data', 2048);

include('framework/FileSystem.php');

spl_autoload_register(array('FileSystem', 'autoload'));

function aDebug() {
    $var = func_get_args();
    $var = count($var) == 1 && isset($var[0]) ? $var[0] : $var;
    $bt=debug_backtrace();
    if(isset($bt[0]) && isset($bt[0]['file'])) echo "<code><pre style='background:yellow; color:red'><a href=\"txmt://open?url=file://{$bt[0]['file']}&line={$bt[0]['line']}\">{$bt[0]['file']} ({$bt[0]['line']})</a></pre></code>";
    var_dump($var);
    return true;
}

session_start();
define('ENV_TYPE', 'dev');
define('PASS_SALT', 'k.jna5v(8&');
define('BASE_PATH', dirname($_SERVER["SCRIPT_FILENAME"]));
define('BASE_URL', 'http://' . $_SERVER["SERVER_NAME"] . substr(BASE_PATH, strlen($_SERVER["DOCUMENT_ROOT"])) . '/');

Builder::$managed_models = Admin::$managed_models = array('Device', 'Questionnaire', 'User');

Request::$default_controller_class = 'Router';
Request::create()->handle();