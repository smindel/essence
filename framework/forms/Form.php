<?php

class Form extends Base
{
    public static function create()
    {
        $backtrace = debug_backtrace();
        $constructor = array($backtrace[1]['object'], $backtrace[1]['function']);
        if (
            is_array($backtrace[1]['args']) &&
            count($backtrace[1]['args']) &&
            is_subclass_of($backtrace[1]['args'][0], 'Model', true)
        ) {
            $modelclass = array_shift($backtrace[1]['args']);
            $objectid = count($backtrace[1]['args']) && is_numeric($backtrace[1]['args'][0]) ? (int)$backtrace[1]['args'][0] : null;
            $object = $modelclass::one($objectid) ?: $modelclass::create();
        } else {
            $object = null;
        }

        $fields = func_get_arg(0);
        $form = parent::create(compact('constructor', 'fields', 'object'));
        foreach ($fields as $field) $field->setForm($form);

        return $form->setData($_REQUEST);
    }

    public function setData($data)
    {
        $error = $callback = false;
        foreach ($this->fields as $field) {
            $submittedvalue = isset($data[$field->name]) ? $data[$field->name] : null;
            if ($field instanceof SubmitFormField && isset($submittedvalue)) {
                $callback = array($this->constructor[0], $field->name);
            }
        }

        if ($callback) foreach ($this->fields as $field) {
            $submittedvalue = isset($data[$field->name]) ? $data[$field->name] : null;
            if (!$field->validate($submittedvalue)) {
                $field->setError('Validation failed');
                $error = true;
            }
            $field->value = isset($submittedvalue) ? $submittedvalue : null;
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
            if (isset($field->value) && isset($field->name)) $data[$field->name] = $field->value;
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