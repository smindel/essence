<?php

class Form extends Base
{
    protected $constructor;
    protected $fields;
    protected $object;

    public function __construct($fields)
    {
        $backtrace = debug_backtrace();
        // aDebug($backtrace);
        $relevanttrace = $backtrace[3];
        $constructor = array($relevanttrace['object'], $relevanttrace['function']);
        if (
            is_array($relevanttrace['args']) &&
            count($relevanttrace['args']) &&
            is_subclass_of($relevanttrace['args'][0], 'Model', true)
        ) {
            $modelclass = array_shift($relevanttrace['args']);
            $objectid = count($relevanttrace['args']) && is_numeric($relevanttrace['args'][0]) ? (int)$relevanttrace['args'][0] : null;
            $object = $modelclass::one($objectid) ?: $modelclass::create();
        } else {
            $object = null;
        }

        $this->constructor = $constructor;
        $this->fields = $fields;
        $this->object = $object;

        foreach ($fields as $field) $field->setForm($this);
    }

    public function getObject()
    {
        return $this->object;
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function setData($data)
    {
        $error = $callback = false;
        foreach ($this->fields as $field) {
            $submittedvalue = isset($data[$field->getName()]) ? $data[$field->getName()] : null;
            if ($field instanceof SubmitFormField && isset($submittedvalue)) {
                $callback = array($this->constructor[0], $field->getName());
            }
        }

        if ($callback) foreach ($this->fields as $field) {
            $submittedvalue = isset($data[$field->getName()]) ? $data[$field->getName()] : null;
            if (!$field->validate($submittedvalue)) {
                $field->setError('Validation failed');
                $error = true;
            }
            $field->setValue($submittedvalue);
        }

        if ($error) {
            return $this->redirectBack();
        } else if ($callback) {
            return call_user_func($callback, $this);
        } else {
            return $this;
        }
    }

    public function getData()
    {
        $data = array();
        foreach ($this->fields as $field) {
            if ($field->getValue() !== null && $field->getName() !== null) $data[$field->getName()] = $field->getValue();
        }
        return $data;
    }

    public function render($method, $data)
    {
        return View::create(get_class($this), $method)->render($data);
    }

    public function __toString()
    {
        return $this->render('index', array('Me' => $this));
    }

    public function action()
    {
        $action = get_class($this->constructor[0]) . '/' . substr($this->constructor[1], 0, -7);
        if (isset($this->object)) {
            $action .= '/' . get_class($this->object) . '/' . $this->object->id;
        }
        return $action;
    }

    public function redirectBack()
    {
        aDebug($this->dependencies['constructor'], $this->dependencies['object']);die();
    }
}