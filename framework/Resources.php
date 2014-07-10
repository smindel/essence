<?php

class Resources extends Base
{
    protected static $required = array();

    public static function add($path, $prio = 50, $type = null)
    {
        self::$required[$path] = array('priority' => $prio, 'type' => $type);
    }

    public static function by_type($type = null)
    {
        $res = array();
        foreach (self::$required as $path => &$options) {
            if (!$options['type']) {
                $segments = explode('.', $path);
                $options['type'] = strtolower(array_pop($segments));
            }
            if ($options['type'] == $type) $res[$path] = $options['priority'];
        }
        ksort($res);
        return array_keys($res);
    }

    public static function css()
    {
        $html = '';
        foreach (self::by_type(__function__) as $path) $html .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"{$path}\">";
        return $html;
    }

    public static function js()
    {
        $html = '';
        foreach (self::by_type(__function__) as $path) $html .= "<script type=\"text/javascript\" src=\"{$path}\"></script>";
        return $html;
    }
}