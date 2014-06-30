<?php

class FileSystem
{
    public static function search_path($path)
    {
        return new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS));
    }

    public static function find($name, $ext = 'php')
    {
        $match = false;
        $paths = array('project', 'modules', 'framework');
        while (!$match && count($paths)) {
            $iterator = self::search_path(array_shift($paths));
            $iterator->rewind();
            while(
                $iterator->valid() &&
                ($fileinfo = $iterator->current()) &&
                !($match = strtolower($fileinfo->getBasename('.' . $ext)) == strtolower($name))
            ) $iterator->next();
        }

        return $match ? $fileinfo : false;
    }

    public static function autoload($classname)
    {
        $fileinfo = self::find($classname);
        if ($fileinfo) {
            include_once($fileinfo->getRealPath());
        } else {
            die("Unknown class '{$classname}'");
        }
    }
}