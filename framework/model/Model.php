<?php

class Model extends Base
{
    protected $db = array(
        'id' => array(
            'type' => 'ID',
            'field' => 'HiddenFormField',
            'label' => 'ID',
            'value' => null
        ),
        'fid' => array(
            'type' => 'FOREIGN:tablename:|RESTRICT|CASCADE',
            'field' => 'HiddenFormField',
            'label' => 'ID',
            'value' => null
        ),
    );
    
    public function db($valtype = 'value', $keytype = 'raw')
    {
        $args = func_get_args();
        $class = get_class($this);
        $valtype = array_shift($args);
        $keytype = array_shift($args);
        $defaults = array('type' => 'auto', 'field' => null, 'value' => null);
        $db = array();
        foreach ($this->db as $col => $options) {
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

    // public function options($field)
    // {
    //     list($metatype, $param1, $param2) = explode(':', $this->db('type')[$field] . ':SET NULL');
    //     if ($metatype == 'FOREIGN') {
    //         $options = $param1::get();
    //         if ($param2 == 'SET NULL') array_unshift($options, $param1::create());
    //     } else if ($metatype == 'LOOKUP') {
    //         $options = $param1::get();
    //     }
    //     return $options;
    // }
    //
    // public function option($field)
    // {
    //     list($metatype, $param1, $param2) = explode(':', $this->db('type')[$field] . ':SET NULL');
    //     if ($metatype == 'FOREIGN') {
    //         foreach ($this->options($field) as $option) if ($option->id == $this->$field) return $option;
    //     } else if ($metatype == 'LOOKUP') {
    //         return $param2;
    //     }
    // }

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
        $fields = array('SecurityID' => SecurityTokenFormField::create('SecurityID'));
        foreach ($this->db as $key => $options) {
            if (empty($options['field'])) continue;
            list($fieldclass) = explode(':', $options['field']);
            $fields[$key] = $fieldclass::create(
                $key,
                isset($options['label']) ? $options['label'] : $key,
                isset($options['value']) ? $options['value'] : null
            );
        }
        if ($this->id) {
            $fields['form_save'] = SubmitFormField::create('form_save', 'ändern');
            $fields['form_delete'] = SubmitFormField::create('form_delete', 'löschen');
        } else {
            $fields['form_save'] = SubmitFormField::create('form_save', 'erstellen');
        }
        return $fields;
    }

    public function title()
    {
        if (isset($this->db['Name'])) return $this->Name;
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
        if (method_exists($this, ($method = 'get' . $key))) {
            return $this->$method();
        } else if (isset($this->db[$key])) {
            list($metatype, $class, $param) = explode(':', $this->db('type')[$key] . ':SET NULL:');
            if ($metatype == 'FOREIGN') {
                return isset($this->db[$key]['value']) ? $class::one($this->db[$key]['value']) : null;
            } else if ($metatype == 'LOOKUP') {
                return isset($this->db['id']['value']) ? $class::get($param, $this->db['id']['value']) : array();
            } else {
                return isset($this->db[$key]['value']) ? $this->db[$key]['value'] : null;
            }
        } else {
            throw new Exception("Undefined property '" . get_class($this) . "->$key'");
        }
    }

    public function __set($key, $val)
    {
        if (method_exists($this, ($method = 'set' . $key))) {
            $this->$method($val);
        } else if ($key == 'id') {
            throw new Exception("Cannot set property '" . get_class($this) . "->$key'");
        } else if (isset($this->db[$key])) {
            list($metatype, $class, $param) = explode(':', $this->db('type')[$field] . ':SET NULL');
            if ($metatype == 'FOREIGN') {
                if ($val instanceof $class) {
                    if ($val->id) {
                        $this->db[$key]['value'] = $val->id;
                    } else {
                        throw new Exception("Related Object has to be saved first: '" . get_class($this) . "->$key'");
                    }
                } else {
                    throw new Exception("You cannot set value on has many relation '" . get_class($this) . "->$key'");
                }
            } else if ($metatype == 'LOOKUP') {
                throw new Exception("You cannot set value on has many relation '" . get_class($this) . "->$key'");
            } else {
                $this->db[$key]['value'] = $val;
            }
        } else {
            throw new Exception("Undefined property '" . get_class($this) . "->$key'");
        }
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

    public function __call($key, $args)
    {
        if (isset($this->db[$key])) {
            list($metatype, $class, $param) = explode(':', $this->db('type')[$key] . ':SET NULL');
            if ($metatype == 'FOREIGN') {
                $options = $class::get();
                if ($param == 'SET NULL') array_unshift($options, $class::create());
                return $options;
            } else if ($metatype == 'LOOKUP') {
                return $class::get();
            } else {
                throw new Exception("Undefined method '" . get_class($this) . "->$key'");
            }
        } else {
            throw new Exception("Undefined method '" . get_class($this) . "->$key'");
        }
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

        $values = array();
        foreach ($this->db as $key => $options) {
            if (!isset($options['value']) || Database::spec($options['field']) === false) continue;
            $values[$key] = $options['value'];
        }

        if ($this->id) {
            if (count($values)) Database::update(self::_base_class(), $values);
        } else {
            $this->db['id']['value'] = Database::insert(self::_base_class(), $values);
        }
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