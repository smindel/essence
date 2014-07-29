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

    public function link()
    {
        return Controller::curr()->getParent()->link('edit', get_class($this), $this->id);
    }

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

    public static function base_class()
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
        $fields = Collection::create(array('SecurityID' => SecurityTokenFormField::create('SecurityID')));
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
            if ($this->canWrite()) $fields['form_save'] = SubmitFormField::create('form_save', 'ändern');
            if ($this->canDelete()) $fields['form_delete'] = SubmitFormField::create('form_delete', 'löschen');
        } else {
            if ($this->canWrite()) $fields['form_save'] = SubmitFormField::create('form_save', 'erstellen');
        }
        return $fields;
    }

    public function canRead()
    {
        return true;
    }

    public function canWrite()
    {
        return true;
    }

    public function canDelete()
    {
        return true;
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
        $list = call_user_func_array(array($modelclass, 'get'), $args);
        return $list->count() ? $list->shift() : false;
    }

    public static function get()
    {
        $args = func_get_args();
        $modelclass = get_called_class();
        if ($modelclass == 'Model') $modelclass = array_shift($args);
        switch (count($args)) {
            case 0: $filter = array(); break;
            case 1: $filter = is_array($args[0]) ? $args[0] : array('id' => (int)$args[0]); break;
            case 2: $filter = array($args[0] => $args[1]); break;
        }
        $list = array();
        foreach (Database::select($modelclass::base_class(), $filter) as $record) {
            $object = $modelclass::create()->hydrate($record);
            if($object->canRead()) $list[$object->id] = $object;
        }
        return Collection::create($list);
    }

    public function __get($key)
    {
        if (method_exists($this, ($method = 'get' . $key))) {
            return $this->$method();
        } else if (isset($this->db[$key])) {
            $type = $this->db('type');
            list($metatype, $class, $param) = explode(':', $type[$key] . ':SET NULL:');
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
            $type = $this->db('type');
            list($metatype, $class, $param) = explode(':', $type[$key] . ':SET NULL:');
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
            $type = $this->db('type');
            list($metatype, $class, $param) = explode(':', $type[$key] . ':SET NULL');
            if ($metatype == 'FOREIGN') {
                $options = $class::get();
                if ($param == 'SET NULL') array_unshift($options, $class::create());
                return $options;
            } else if ($metatype == 'LOOKUP') {
                return $class::get();
            }
        } else {
            return call_user_func_array('parent::__call', func_get_args());
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
        if (!$this->canWrite()) throw new Exception("You cannot write");

        if (method_exists($this, 'beforeWrite')) if (!$this->beforeWrite()) return $this;

        $values = array();
        foreach ($this->db as $key => $options) {
            if (!isset($options['value']) || isset($options['type']) && Database::spec($options['type']) === false) continue;
            $values[$key] = $options['value'];
        }

        if ($this->id) {
            if (count($values)) Database::update(self::base_class(), $values);
        } else {
            $this->db['id']['value'] = Database::insert(self::base_class(), $values);
        }
        if (method_exists($this, 'afterWrite')) $this->afterWrite();
        return $this;
    }

    public function delete()
    {
        if (!$this->canDelete()) throw new Exception("You cannot delete");

        if (method_exists($this, 'beforeDelete')) if (!$this->beforeDelete()) return $this;
        Database::delete(self::base_class(), $this->db['id']['value']);
        unset($this->db['id']['value']);
        if (method_exists($this, 'afterDelete')) $this->afterDelete();
        return $this;
    }
}