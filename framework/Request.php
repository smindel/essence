<?php

class Request extends Base
{
    public static $default_controller_class = 'DefaultController';

    protected $requesturi;
    protected $requestdata;
    protected $availablesegments;
    protected $consumedsegments = array();

    public function __construct($requesturi = null, $requestdata = null)
    {
        $this->requesturi = $requesturi ?: self::relative_url(self::absolute_url($_SERVER["REQUEST_URI"], true));;
        $this->requestdata = $requestdata ?: $_REQUEST;
        $this->availablesegments = explode('/', parse_url($this->requesturi, PHP_URL_PATH));
    }

    public function getUri($absolute = false)
    {
        return $absolute ? self::absolute_url($this->requesturi) : $this->requesturi;
    }

    public function remaining()
    {
        return count($this->availablesegments);
    }

    public function consume($numsegments = false)
    {
        if ($numsegments === false) {
            return ($this->consumedsegments[] = array_shift($this->availablesegments));
        }
        $segments = array();
        while ($numsegments-- && count($this->availablesegments)) {
            $segments[] = $this->consumedsegments[] = array_shift($this->availablesegments);
        }
        return $segments;
    }

    public function peek($numsegments = false)
    {
        if ($numsegments === false) return reset($this->availablesegments);
        return array_slice($this->availablesegments, 0, $numsegments);
    }

    public function handle()
    {
        return Base::create($this->consume())->handleRequest($this);
    }

    public static function relative_url($uri)
    {
        if (substr($uri, 0, 4) == 'http' && substr($uri, 0, strlen(BASE_URL)) == BASE_URL) return substr($uri, strlen(BASE_URL));
        return $uri;
    }

    public static function absolute_url($uri, $justprependserver = false)
    {
        if (substr($uri, 0, 4) == 'http') return $uri;
        if ($justprependserver) return 'http://' . $_SERVER['SERVER_NAME'] . $uri;
        return BASE_URL . $uri;
    }

    public function isAjax()
    {
        if ($this->getRaw('forceajax')) return true;
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }

    public function getRaw()
    {
        $args = func_get_args();
        $raw = $this->requestdata;
        while (count($args)) {
            $curr = array_shift($args);
            if (!isset($raw[$curr])) return null;
            $raw = $raw[$curr];
        }
        return $raw;
    }
}