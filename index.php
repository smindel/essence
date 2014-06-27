<?php

function __autoload($classname) {
    $filename = "./". $classname .".php";
    if (file_exists($filename)) {
        include_once($filename);
    } else {
        die("Unknown class '{$classname}'");
    }
}

function aDebug() {
    $var = func_get_args();
    $var = count($var) == 1 && isset($var[0]) ? $var[0] : $var;
    $bt=debug_backtrace();
    if(isset($bt[0]) && isset($bt[0]['file'])) echo "<code><pre style='background:yellow; color:red'><a href=\"txmt://open?url=file://{$bt[0]['file']}&line={$bt[0]['line']}\">{$bt[0]['file']} ({$bt[0]['line']})</a></pre></code>";
    var_dump($var);
}

session_start();
define('ENV_TYPE', 'dev');
define('BASE_PATH', dirname($_SERVER["SCRIPT_FILENAME"]));
define('BASE_URL', 'http://' . $_SERVER["SERVER_NAME"] . substr(BASE_PATH, strlen($_SERVER["DOCUMENT_ROOT"])) . '/');

// aDebug(BASE_PATH, BASE_URL);die();

Request::$default_controller_class = 'Router';

Request::create()->handle();