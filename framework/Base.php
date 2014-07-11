<?php

abstract class Base
{
    protected static $replaced = array();

    public static function create()
    {
        $args = func_get_args();
        $class = get_called_class();
        if ($class == __CLASS__) {
            if (count($args)) {
                $class = array_shift($args);
            } else {
                throw new Exception("Invalid call to factory");
            }
        }
        $class = isset(self::$replaced[$class]) ? self::$replaced[$class] : $class;

        if (count($args)) {
            $reflector = new ReflectionClass($class);
            return $reflector->newInstanceArgs($args);
        } else {
            return new $class();
        }
    }

    public static function replace_class($old, $new)
    {
        self::$replaced[$old] = $new;
        foreach (self::$replaced as $key => $val) if ($val == $old) self::$replaced[$key] = $new;
    }

    // logging and caching
    protected $cache = array();
    public function __call($key, $args)
    {
        $cache = $cached = $log = false;
        $method = $key;
        if ($key[0] == '_') {
            $method = substr($method, 1);
            $cache = true;
        }
        if (substr($key, -1) == '_') {
            $method = substr($method, 0, -1);
            $log = true;
        }
        if (!method_exists($this, $method)) {
            throw new Exception("Undefined method '" . get_class($this) . "->{$key}()'");
        }
        $signature = $key . '=' . json_encode($args);
        $start = microtime();
        $result = $cache && ($cached = isset($this->cache[$signature])) ? $this->cache[$signature] : call_user_func_array(array($this, $method), $args);
        $duration = microtime() - $start;
        if ($cache) {
            $this->cache[$signature] = $result;
        }
        if ($log) {
            if ($duration > 1) {
                $duration = substr($duration . 00000, 0, 5) . 's';
            } else if ($duration * 1000 > 1) {
                $duration = substr(($duration * 1000) . 00000, 0, 5) . 'ms';
            } else {
                $duration = substr(($duration * 1000000) . 00000, 0, 5) . 'µs'; 
            }
            Logger::create()->debug('{method}({arguments}) : {duration}{flag} = {result}', array(
                'flag' => $cached ? '*' : '',
                'method' => get_class($this) . '->' . $method,
                'arguments' => printr($args),
                'duration' => $duration,
                'result' => printr($result),
            ));
        }
        return $result;
    }
}

function printr($data)
{
    return str_replace(array("\n", "\t", '    '), array('', '', ''), print_r($data, true));
}