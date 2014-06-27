<?php

class Form extends Controller
{
    public static function create()
    {
        $backtrace = debug_backtrace();
        $constructor = array($backtrace[1]['object'], $backtrace[1]['function']);
        $fields = func_get_arg(0);
        $form = parent::create(compact('constructor', 'fields'));
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

    public function __toString()
    {
        return $this->render('index', array('Me' => $this));
    }

    public function action()
    {
        return get_class($this->constructor[0]) . '/' . substr($this->constructor[1], 0, -7);
    }
}