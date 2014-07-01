<?php

abstract class FormField extends Base
{
    public function setForm(Form $form)
    {
        $this->form = $form;
    }

    public function getId()
    {
        return str_replace('/', '_', $this->form->action() . '_' . $this->name);
    }

    public function validate($value)
    {
        return isset($this->validator) ? call_user_func($this->validator, $value, $this, $form) : true;
    }

    public function setValidator($validator)
    {
        $this->validator = $validator;
        return $this;
    }

    public function setError($msg)
    {
        $_SESSION[$this->getId() . '_error'] = $msg;
    }

    public function getError($keep = false)
    {
        $msg = false;
        if (isset($_SESSION[$this->getId() . '_error'])) {
            $msg = $_SESSION[$this->getId() . '_error'];
            if (!$keep) unset($_SESSION[$this->getId() . '_error']);
        }
        return $msg;
    }

    public function __toString()
    {
        return (string)$this->getError();
    }
}