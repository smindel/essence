<?php

class Model extends Base
{
    protected $db = array(
        'id' => array(
            'type' => 'ID',
            'field' => 'HiddenFormField',
            'label' => 'ID',
            'value' => 0
        ),
        'fid' => array(
            'type' => 'FOREIGN:tablename:|RESTRICT|CASCADE',
            'field' => 'HiddenFormField',
            'label' => 'ID',
            'value' => 0
        ),
    );
    
    public static function db($valtype = 'value', $keytype = 'raw')
    {
        $args = func_get_args();
        $class = get_called_class();
        if ($class == 'Model') $class = array_shift($args);
        $object = $class::create();
        $valtype = array_shift($args);
        $keytype = array_shift($args);
        $defaults = array('type' => 'auto', 'field' => null, 'value' => null);
        $db = array();
        foreach ($object->db as $col => $options) {
            $defaults['label'] = $col;
            switch ($keytype) {
                case 'colon': $key = ':' . $col; break;
                case 'doublequoted': $key = '"' . $col . '"'; break;
                default: $key = $col;
            }
            switch ($valtype) {
                case 'colon': $val = ':' . $col; break;
                case 'doublequoted': $val = '"' . $col . '"'; break;
                case 'key': $val = $col;
                case 'singlequoted': $val = isset($options['value']) ? "'{$options['value']}'" : "''"; break;
                case 'options': $val = array_merge($defaults, $options); break;
                default: $val = isset($options[$valtype]) ? $options[$valtype] : (isset($defaults[$valtype]) ? $defaults[$valtype] : null); break;
            }
            $db[$key] = $val;
        }
        return $db;
    }

    public function options($field)
    {
        list($metatype, $param1, $param2) = explode(':', $this->db('type')[$field] . ':SET NULL');
        $options = $param1::get();
        if ($param2 == 'SET NULL') array_unshift($options, $param1::create());
        return $options;
    }

    public function option($field)
    {
        foreach ($this->options($field) as $option) if ($option->id == $this->$field) return $option;
    }

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

    public function getFields()
    {
        $fields = array(SecurityTokenFormField::create('SecurityID'));
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
            $fields[] = SubmitFormField::create('form_save', 'ändern');
            $fields[] = SubmitFormField::create('form_delete', 'löschen');
        } else {
            $fields[] = SubmitFormField::create('form_save', 'erstellen');
        }
        return $fields;
    }

    public function title()
    {
        return get_class($this) . " ({$this->id})";
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
        if (method_exists($this, 'beforeWrite')) if (!$this->beforeWrite()) return $this;
        foreach ($this->db as $name => $field) {
            if (!isset($field['value']) || $name == 'id' && !is_numeric($field['value'])) continue;
            $props[$name] = $field['value'];
        }
        $this->db['id']['value'] = Database::replace(self::_base_class(), $props);
        if (method_exists($this, 'afterWrite')) $this->afterWrite();
        return $this;
    }

    public function delete()
    {
        if (method_exists($this, 'beforeDelete')) if (!$this->beforeDelete()) return $this;
        Database::delete(self::_base_class(), $this->db['id']['value']);
        unset($this->db['id']['value']);
        if (method_exists($this, 'afterDelete')) $this->afterDelete();
        return $this;
    }
}