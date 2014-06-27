<?php

abstract class FormField extends Base
{
    public function setForm(Form $form)
    {
        $this->Form = $form;
    }

    public function validate($value)
    {
        return isset($this->validator) ? call_user_func($this->validator, $value) : true;
    }

    public function setValidator($validator)
    {
        $this->validator = $validator;
        return $this;
    }

    public function setError($msg)
    {
        $_SESSION[$this->name . '_error'] = $msg;
    }

    public function getError($keep = false)
    {
        $msg = false;
        if (isset($_SESSION[$this->name . '_error'])) {
            $msg = $_SESSION[$this->name . '_error'];
            if (!$keep) unset($_SESSION[$this->name . '_error']);
        }
        return $msg;
    }

    public function __toString()
    {
        return (string)$this->getError();
    }
}