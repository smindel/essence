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
        'parent' => array(
            'type' => 'FOREIGN:RemoteClassName:|RESTRICT|CASCADE(|SET NULL)',
            'field' => 'HasOneFormField',
            'label' => 'Parent',
            'value' => null
        ),
        'children' => array(
            'type' => 'LOOKUP:RemoteClassName:RemoteJoinField',
            'field' => 'HasManyFormField|RelationFormField:add',
            'label' => 'Children',
        ),
    );

    public function getProperty($property, $key = 'type')
    {
        if (!isset($this->db[$property])) return false;
        if (isset($this->db[$property][$key])) return $this->db[$property][$key];
        switch ($key) {
            case 'type': return 'DEFAULT';
            case 'label': return $property;
            case 'field': return $this->getDefaultFormFieldClass($property);
            case 'value': return null;
        }
    }

    public function getProperties($values = 'type')
    {
        $properties = array();
        foreach ($this->db as $prop => $spec) {
            $properties[$prop] = $this->getProperty($prop, $values);
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
        list($type) = explode(':', $this->getProperty($propertyname));
        switch ($type) {
            case 'ID': return 'HiddenFormField';
            case 'DATE': return 'DateFormField';
            case 'DATETIME': return 'DatetimeFormField';
            case 'BOOL': return 'CheckboxFormField';
            case 'FOREIGN': return 'HasOneFormField';
            case 'LOOKUP': return 'HasManyFormField';
            default: return 'TextFormField';
        }
    }

    public function getFields()
    {
        $fields = Collection::create(array(
            'Header' => HtmlFormField::create('Header', null, "<h1>{$this->title()}</h1>"),
            'SecurityID' => SecurityTokenFormField::create('SecurityID'),
        ));
        foreach ($this->getProperties('field') as $key => $fieldtype) {
            if (!$fieldtype) continue;
            list($fieldclass) = explode(':', $fieldtype);
            $fields[$key] = $fieldclass::create(
                $key,
                $this->getProperty($key, 'label'),
                $this->getProperty($key, 'value')
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
            list($metatype, $class, $param) = explode(':', $this->getProperty($key) . ':SET NULL:');
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
            list($metatype, $class, $param) = explode(':', $this->getProperty($key) . ':SET NULL:');
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

    public function __call($key, $args)
    {
        if (isset($this->db[$key])) {
            list($metatype, $class, $param) = explode(':', $this->getProperty($key) . ':SET NULL');
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