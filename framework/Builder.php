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

        $tables = Database::tables();
        foreach (self::$managed_models as $modelclass) {
            $model = $modelclass::create();
            $base = $modelclass::base_class();
            if (!in_array($modelclass, $tables)) {
                $specs = array();
                Database::create_table($base, $model->db('type'));
                $messages[] = array('type' => 'good', 'text' => "table {$base} created");
            } else if (($diff = array_diff_key($model->db('type'), Database::table($modelclass))) && count($diff)) {
                foreach ($diff as $col => $spec) {
                    $spec = Database::spec($spec);
                    if ($spec === false) continue;
                    Database::query("ALTER TABLE \"{$base}\" ADD COLUMN \"{$col}\" {$spec}");
                    $messages[] = array('type' => 'good', 'text' => "column {$col} for table {$base} created");
                }
            }
        }

        Database::query('VACUUM');
        $messages[] = array('type' => 'good', 'text' => "database vacuumed");

        return array('messages' => $messages);
    }
}