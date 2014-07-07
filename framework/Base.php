<?php

abstract class Base
{
    public static function create()
    {
        $args = func_get_args();
        $class = get_called_class();
        if ($class == __CLASS__) {
            if (count($args)) {
                $class = array_shift($args);
            } else {
                throw new Exception("Invalid contructor parameters for '{$class}'");
            }
        }

        if (count($args)) {
            $reflector = new ReflectionClass($class);
            return $reflector->newInstanceArgs($args);
        } else {
            return new $class();
        }
    }
}