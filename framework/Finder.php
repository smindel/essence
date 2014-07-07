<?php

class Finder
{
    const REGEX = 1;
    const CASE_SENSITIVE = 2;
    const RETURN_ABSOLUTE = 4;
    const RETURN_PATH = 8;
    const RETURN_NAME = 16;
    const RETURN_ALL = 32;

    public static $blacklist = array();

    public static function find($pattern, $root = BASE_PATH, $mode = 0)
    {
        $all = array();
        $root = $root[0] == DIRECTORY_SEPARATOR ? $root : BASE_PATH . DIRECTORY_SEPARATOR . $root;
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS));
        foreach ($iterator as $fileinfo) {
            $pathname = $fileinfo->getPathname();
            if (!self::match($pathname, $pattern, $mode)) continue;
            $prepared = self::prepare($fileinfo, $mode);
            if (~$mode & self::RETURN_ALL) return $prepared;
            $all[] = $prepared;
        }
        return $mode & self::RETURN_ALL ? $all : false;
    }

    protected static function prepare(SPLFileInfo $fileinfo, $mode)
    {
        $pathname = $fileinfo->getPathname();
        if (~$mode & self::RETURN_ABSOLUTE) $pathname = ltrim(substr($pathname, strlen(BASE_PATH)), DIRECTORY_SEPARATOR);
        switch (true) {
            case $mode & self::RETURN_PATH: return dirname($pathname);
            case $mode & self::RETURN_NAME: return basename($pathname);
            default: return $pathname;
        }
    }

    protected static function match($haystack, $needle, $mode)
    {
        if ($mode & self::REGEX) {
            $pattern = '/' . $needle . '/';
            if (~$mode & self::CASE_SENSITIVE) $pattern .= 'i';
            if (!preg_match($pattern, $haystack)) return false;
        } else {
            if (~$mode & self::CASE_SENSITIVE) {
                $needle = strtolower($needle);
                $haystack = strtolower($haystack);
            }
            if (basename($haystack) != $needle) return false;
        }
        foreach (self::$blacklist as $pattern) {
            if (preg_match($pattern, $haystack)) return false;
        }
        return true;
    }
}