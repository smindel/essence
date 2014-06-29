<?php

class SecurityTokenFormField extends FormField
{
    public static function create()
    {
        $name = 'SecurityID';
        return parent::create(compact('name'));
    }

    public function __toString()
    {
        return '<div>' . $this->getError() . "</div><input type=\"hidden\" id=\"SecurityID\" name=\"SecurityID\" value=\"" . $this->value() . "\">";
    }

    public function value()
    {
        if (isset($_SESSION['SecurityID'])) {
            return $_SESSION['SecurityID'];
        } else {
            return $_SESSION['SecurityID'] = sha1(uniqid());
        }
    }

    public function validate($value)
    {
        return isset($_SESSION['SecurityID']) && $_SESSION['SecurityID'] == $value && parent::validate($value);
    }
}