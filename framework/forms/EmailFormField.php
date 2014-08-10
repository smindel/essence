<?php

class EmailFormField extends FormField
{
    public function validate($value)
    {
        if ($value && !preg_match('/.+@.+/', $value)) {
            $this->setError('Please enter a valid email address.');
            return false;
        }
        return parent::validate($value);
    }
}