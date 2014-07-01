<?php

class Request extends Base
{
    public static $default_controller_class = 'DefaultController';

    public static function create()
    {
        $absoluterequestpath = parse_url($_SERVER["DOCUMENT_ROOT"] . $_SERVER["REQUEST_URI"],  PHP_URL_PATH);
        $relativerequestpath = substr($absoluterequestpath, 0, strlen(BASE_PATH)) == BASE_PATH ? trim(substr($absoluterequestpath, strlen(BASE_PATH)), '/') : false;
        $segments = $relativerequestpath ? explode('/', $relativerequestpath) : array();
        if (count($segments)) array_push($segments, (list($last, $format) = explode('.', array_pop($segments) . '.html')) ? $last : $last);

        $controllerclass = count($segments) ? array_shift($segments) : self::$default_controller_class;
        $methodname = count($segments) ? array_shift($segments) : 'index';
        $parameters = $segments;

        return parent::create(compact('controllerclass', 'methodname', 'parameters', 'responseformat'));
    }

    public function handle()
    {
        return Base::create($this->controllerclass)->handleRequest($this);
    }
}