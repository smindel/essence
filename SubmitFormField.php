<?php

class SubmitFormField extends FormField
{
    public static function create()
    {
        $args = func_get_args();
        $name = array_shift($args);
        $label = count($args) ? array_shift($args) : $name;
        return parent::create(compact('name', 'label'));
    }

    public function __toString()
    {
        return "<input type=\"submit\" name=\"{$this->name}\" value=\"{$this->label}\">";
    }
}