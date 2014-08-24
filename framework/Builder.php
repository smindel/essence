<?php

class Builder extends Controller
{
    public static $managed_models = array();

    public function build_action()
    {
        $messages = array();

        $count = 0;
        foreach (Finder::find('\.inc$', 'cache', Finder::RETURN_ALL + Finder::REGEX) as $filepath) {
            unlink($filepath);
            $count++;
        }
        $messages[] = array('type' => 'good', 'text' => $count . ' template files deleted');

        foreach (self::$managed_models as $modelclass) {
            $message = $this->build($modelclass);
            if ($message) $messages[] = $message;
        }

        Database::query('VACUUM');
        $messages[] = array('type' => 'good', 'text' => "database vacuumed");

        return array('messages' => $messages);
    }

    public function build($modelclass)
    {
        $tables = Database::tables();
        $message = false;
        $model = $modelclass::create();
        $base = $modelclass::base_class();
        if (!in_array($modelclass, $tables)) {
            $specs = array();
            foreach ($model->getProperties() as $property => $void) {
                foreach (array('type', 'unique', 'null', 'default', 'size', 'remoteclass', 'oninvalid') as $key) {
                    $specs[$property][$key] = $model->getProperty($property, $key);
                }
            }
            Database::create_table($base, $specs);
            $message = array('type' => 'good', 'text' => "table {$base} created");
        } else if (($diff = array_diff_key($model->getProperties(), Database::table($modelclass))) && count($diff)) {
            $cols = array();
            foreach ($diff as $col => $specs) {
                $spec = array();
                foreach (array('type', 'unique', 'null', 'default', 'size', 'remoteclass', 'oninvalid') as $key) {
                    $spec[$key] = $model->getProperty($col, $key);
                }
                if (Database::spec($spec) === false) continue;
                Database::add_column($base, $col, $spec);
                $cols[] = $col;
            }
            if (count($cols)) $message = array('type' => 'good', 'text' => "column(s) " . implode(', ', $cols) . " for table {$base} created");
        }
        return $message;
    }
}