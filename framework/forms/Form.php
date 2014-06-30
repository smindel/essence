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
        if (isset($_REQUEST['SecurityID'])) {
            $error = false;
            foreach ($fields as $field) {
                if (!$field->validate($_REQUEST[$field->name])) {
                    $field->setError('Validation failed');
                    $error = true;
                }
                if ($field instanceof SubmitFormField && isset($_REQUEST[$field->name])) {
                    $callback = array($form->constructor[0], $field->name);
                }
            }
            if ($error) return $form->redirectBack();
            return call_user_func($callback, $form, $_REQUEST);
        } else {
            return $form;
        }
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