<?php

class Request extends Base
{
    public static $default_controller_class = 'DefaultController';

    protected $controllerclass;
    protected $methodname;
    protected $parameters;

    public function __construct($requesturi)
    {
        $absoluterequestpath = parse_url($_SERVER["DOCUMENT_ROOT"] . $requesturi,  PHP_URL_PATH);
        $relativerequestpath = substr($absoluterequestpath, 0, strlen(BASE_PATH)) == BASE_PATH ? trim(substr($absoluterequestpath, strlen(BASE_PATH)), '/') : false;
        $segments = $relativerequestpath ? explode('/', $relativerequestpath) : array();

        $controllerclass = count($segments) ? array_shift($segments) : self::$default_controller_class;
        $methodname = count($segments) ? array_shift($segments) : 'index';
        $parameters = $segments;

        $this->controllerclass = $controllerclass;
        $this->methodname = $methodname;
        $this->parameters = $parameters;
    }

    public function getMethodname()
    {
        return $this->methodname;
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    public function handle()
    {
        return Base::create($this->controllerclass)->handleRequest($this);
    }

    public static function relative_url($uri)
    {
        if (substr($uri, 0, 4) == 'http' && substr($uri, 0, strlen(BASE_URL)) == BASE_URL) return substr($uri, strlen(BASE_URL));
        return $uri;
    }

    public static function absolute_url($uri)
    {
        if (substr($uri, 0, 4) != 'http') return BASE_URL . $uri;
        return $uri;
    }
}