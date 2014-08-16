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
        'Name' => array(
            'type' => 'TEXT',
            'size' => 32,
            'field' => 'TextFormField',
            'label' => 'Name',
            'value' => null
        ),
        'parent' => array(
            'type' => 'FOREIGN',
            'remoteclass' => 'someclass',
            'oninvalid' => 'SET NULL*|RESTRICT|CASCADE',
            'field' => 'ObjectFormField',
            'label' => 'Parent',
            'value' => null
        ),
        'children' => array(
            'type' => 'LOOKUP',
            'remoteclass' => 'RemoteClassName',
            'remotefield' => 'RemoteJoinField',
            'field' => 'CollectionFormField',
            'label' => 'Children',
        ),
    );

    public function getProperty($property, $key = null)
    {
        if (!isset($this->db[$property])) return false;
        if (!$key) return $this->db[$property];
        if (isset($this->db[$property][$key])) return $this->db[$property][$key];
        switch ($key) {
            case 'type': throw new Exception('Type must be defined');
            case 'oninvalid': return 'SET NULL';
            case 'label': return $property;
            case 'field': return $this->getDefaultFormFieldClass($property);
            default: return null;
        }
    }

    public function getProperties($key = null)
    {
        if (!$key) return $this->db;
        $properties = array();
        foreach ($this->db as $prop => $spec) {
            $properties[$prop] = $this->getProperty($prop, $key);
        }
        return $properties;
    }

    public static function base_class()
    {
        $class = get_called_class();
        if ($class == 'Model') throw new Exception('Cannot find base for class Model');
        while (get_parent_class($class) != 'Model' && $i < 100) $class = get_parent_class($class);
        return $class;
    }

    public function getDefaultFormFieldClass($propertyname)
    {
        if (isset($this->db[$propertyname]['field'])) return $this->db[$propertyname]['field'];
        switch ($this->getProperty($propertyname, 'type')) {
            case 'ID': return 'HiddenFormField';
            case 'INT': return 'NumberFormField';
            case 'FLOAT': return 'NumberFormField';
            case 'DATE': return 'DateFormField';
            case 'DATETIME': return 'DatetimeFormField';
            case 'BOOL': return 'CheckboxFormField';
            case 'FOREIGN': return 'ObjectFormField';
            case 'LOOKUP': return 'CollectionFormField';
            default: return 'TextFormField';
        }
    }

    public function getFields()
    {
        $fields = Collection::create();
        foreach ($this->getProperties('field') as $key => $fieldclass) {
            if (!$fieldclass) continue;
            switch (true) {
                case is_a($fieldclass, 'ObjectFormField', true):
                    $fields[$key] = $fieldclass::create($key, $this->getProperty($key, 'label'), $this->getProperty($key, 'value'), $this->$key(), $this->getProperty($key, 'remoteclass'), $this->getProperty($key, 'oninvalid'))
                                    ->setFieldSet('Main');
                    break;
                case is_a($fieldclass, 'CollectionFormField', true):
                    if ($this->id) $fields[$key] = $fieldclass::create($key, $this->getProperty($key, 'label'), $this->$key, $this->$key(), $this->getProperty($key, 'remoteclass'))
                                    ->setFieldSet($key)
                                    ->setHydrate(array($this->getProperty($key, 'remotefield') => $this->id));
                    break;
                default:
                    $fields[$key] = $fieldclass::create($key, $this->getProperty($key, 'label'), $this->getProperty($key, 'value'))->setFieldSet('Main');
            }
            if (isset($fields[$key])) {
                $fields[$key]->setRequired($this->getProperty($key, 'required'));
                $fields[$key]->setCheck($this->getProperty($key, 'check'));
            }
        }
        $fields['SecurityID'] = SecurityTokenFormField::create('SecurityID');
        
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
        if (isset($this->db['name'])) return $this->name;
        return get_class($this) . " ({$this->id})";
    }

    public static function one()
    {
        $args = func_get_args();
        $modelclass = get_called_class();
        $list = call_user_func_array(array($modelclass, 'get'), $args);
        return $list->count() ? $list->shift() : null;
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
            switch ($this->getProperty($key, 'type')) {
                case 'FOREIGN': return isset($this->db[$key]['value']) ? Model::one($this->getProperty($key, 'remoteclass'), $this->db[$key]['value']) : null;
                case 'LOOKUP': return isset($this->db['id']['value']) ? Model::get($this->getProperty($key, 'remoteclass'), $this->getProperty($key, 'remotefield'), $this->db['id']['value']) : Collection::create();
                default: return isset($this->db[$key]['value']) ? $this->db[$key]['value'] : null;
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
            switch ($this->getProperty($key, 'type')) {
                case 'FOREIGN':
                    if (is_a($val, $this->getProperty($key, 'remoteclass'), true)) {
                        if ($val->id) {
                            $this->db[$key]['value'] = $val->id;
                        } else {
                            aDebug($this, $val);
                            throw new Exception("Related Object has to be saved first: '" . get_class($this) . "->$key'");
                        }
                    } else {
                        throw new Exception("You cannot set value on has many relation '" . get_class($this) . "->$key'");
                    }
                    break;
                case 'LOOKUP':
                    throw new Exception("You cannot set value on has many relation '" . get_class($this) . "->$key'");
                default:
                    $this->db[$key]['value'] = $val;
            }
        } else {
            throw new Exception("Undefined property '" . get_class($this) . "->$key'");
        }
    }

    public function __call($key, $args)
    {
        if (isset($this->db[$key])) {
            switch ($this->getProperty($key, 'type')) {
                case 'FOREIGN':
                case 'LOOKUP': return Model::get($this->getProperty($key, 'remoteclass'));
            }
        } else {
            return call_user_func_array('parent::__call', func_get_args());
        }
    }

    public function hydrate($record)
    {
        foreach ($record as $key => $val) {
            if(!($type = $this->getProperty($key, 'type'))) continue;
            if ($type == 'FOREIGN' && !$val) $val = null;
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
            $required = $this->getProperty($key, 'required');
            $value = $this->getProperty($key, 'value');
            if (!empty($required) && empty($value)) throw new Exception("Value " . get_class($this) . "->{$key} is required.");
            if (!array_key_exists('value', $options) || isset($options['type']) && Database::spec($options) === false) continue;
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