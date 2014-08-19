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
        $this->requestdata = $requestdata ?: $_REQUEST;

        $requesturi = $requesturi ?: 'http://' . SERVER_NAME . $_SERVER["REQUEST_URI"];
        $this->requesturi = trim(self::relative_url($requesturi), '/');
        $this->availablesegments = explode('/', parse_url(trim($this->requesturi, '/'), PHP_URL_PATH));
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

    public static function relative_url($uri, $strict = false)
    {
        if (substr($uri, 0, strlen(BASE_URL)) == BASE_URL) {
            return substr($uri, strlen(BASE_URL));
        }
        if ($strict) {
            throw new Exception("'$uri' is not an absolute URL of this project and cannot be made relative.");
        } else {
            return $uri;
        }
    }

    public static function absolute_url($uri, $strict = false)
    {
        if (substr($uri, 0, 7) != 'http://' && substr($uri, 0, 8) != 'https://') {
            return BASE_URL . ltrim($uri, '/');
        }
        if ($strict) {
            throw new Exception("'$uri' is not a relative URL and cannot be made absolute.");
        } else {
            return $uri;
        }
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