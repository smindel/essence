<?php

class EmailFormField extends TextFormField
{
    public function __toString()
    {
        return '<div>' . $this->getError() . "</div><label for=\"{$this->name}\">{$this->label}</label><input type=\"email\" id=\"{$this->name}\" name=\"{$this->name}\" value=\"{$this->value}\">";
    }

    public function validate($value)
    {
        return (!$value || preg_match('/.+@.+/', $value)) && parent::validate($value);
    }
}