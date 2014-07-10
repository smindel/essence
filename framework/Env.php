<?php

class Env extends Base
{
    protected static $stack = array();

    protected $services = array();

    public static function curr()
    {
        if (empty($stack)) self::push();
        return self::push(self::pop());
    }

    public static function push(Env $env = null)
    {
        $env = $env ?: self::create();
        return (self::$stack[] = $env);
    }

    public static function pop()
    {
        return array_pop(self::$stack);
    }

    public function set($name, Service $service)
    {
        $this->services[$name] = $service;
    }

    public function get($name)
    {
        return $this->services[$name];
    }
}