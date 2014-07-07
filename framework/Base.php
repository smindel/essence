<?php

abstract class Base
{
    protected $dependencies = array();

    final public function __construct($dependencies = array()) {
        $this->dependencies = (array) $dependencies;
    }

    public static function create()
    {
        $args = func_get_args();
        $class = get_called_class();
        if ($class == 'Base') {
            if (is_array($args) && count($args)) {
                $class = array_shift($args);
            } else {
                throw new Exception("Invalid contructor parameters for '{$class}'");
            }
        }
        return count($args) ? new $class($args[0]) : new $class();
    }
}