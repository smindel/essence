<?php

class Model extends Base
{
    public static function _base_class()
    {
        $class = get_called_class();
        if ($class == 'Model') throw new Exception('Cannot find base for class Model');
        $i = 0;
        while (get_parent_class($class) != 'Model' && $i < 100) {
            $class = get_parent_class($class);
            $i++;
        }
        return $class;
    }

    public static function _build()
    {
        $class = get_called_class();
        $specs = array();
        $model = $class::create();
        foreach ($model->db as $key => $options) $specs[$key] = isset($options['type']) ? $options['type'] : 'auto';
        Database::build(self::_base_class(), $specs);
    }

    public function getFields()
    {
        $fields = array(SecurityTokenFormField::create());
        foreach ($this->db as $key => $options) {
            if (empty($options['field'])) continue;
            $fieldclass = $options['field'];
            $fields[] = $fieldclass::create(
                $key,
                isset($options['label']) ? $options['label'] : $key,
                isset($options['value']) ? $options['value'] : null
            );
        }
        if ($this->id) {
            $fields[] = SubmitFormField::create('form_submit', 'ändern');
            $fields[] = SubmitFormField::create('form_submit', 'löschen');
        } else {
            $fields[] = SubmitFormField::create('form_submit', 'erstellen');
        }
        return $fields;
    }

    public static function one()
    {
        $args = func_get_args();
        $modelclass = get_called_class();
        if ($modelclass == 'Model') $modelclass == array_shift($args);
        $list = call_user_func_array(array($modelclass, 'get'), $args);
        return count($list) ? array_shift($list) : false;
    }

    public static function get()
    {
        $args = func_get_args();
        $modelclass = get_called_class();
        if ($modelclass == 'Model') $modelclass == array_shift($args);
        switch (count($args)) {
            case 0: $filter = array(); break;
            case 1: $filter = is_array($args[0]) ? $args[0] : array('id' => (int)$args[0]); break;
            case 2: $filter = array($args[0] => $args[1]); break;
        }
        $list = array();
        foreach (Database::select($modelclass::_base_class(), $filter) as $record) {
            $list[] = $modelclass::create()->hydrate($record);
        }
        return $list;
    }

    public function __get($key)
    {
        if (!isset($this->db[$key])) throw new Exception("Undefined property '" . get_class($this) . "->$key'");
        return isset($this->db[$key]['value']) ? $this->db[$key]['value'] : null;
    }

    public function __set($key, $val)
    {
        if ($key == 'id') throw new Exception("Cannot set property '" . get_class($this) . "->$key'");
        if (!isset($this->db[$key])) throw new Exception("Undefined property '" . get_class($this) . "->$key'");
        $this->db[$key]['value'] = $val;
    }

    public function __unset($key)
    {
        if (!isset($this->db[$key])) throw new Exception("Undefined property '" . get_class($this) . "->$key'");
        unset($this->db[$key]['value']);
    }

    public function __isset($key)
    {
        return isset($this->db[$key]) && isset($this->db[$key]['value']);
    }

    public function hydrate($record)
    {
        foreach ($record as $key => $val) {
            if (!isset($this->db[$key])) continue;
            $this->db[$key]['value'] = $val;
        }
        return $this;
    }

    public function write()
    {
        foreach ($this->db as $name => $field) {
            if ($name == 'id' && !is_numeric($field['value'])) continue;
            $props[$name] = $field['value'];
        }
        $this->db['id']['value'] = Database::replace(self::_base_class(), $props);
        return $this;
    }
}