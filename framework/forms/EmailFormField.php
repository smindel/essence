<?php

class EmailFormField extends FormField
{
    public function validate($value)
    {
        return (!$value || preg_match('/.+@.+/', $value)) && parent::validate($value);
        return preg_match('/^[a-zA-Z0-9.!#$%&\'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/', $value);
    }
}