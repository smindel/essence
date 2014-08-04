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
}