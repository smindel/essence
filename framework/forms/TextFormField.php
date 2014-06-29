<?php

class TextFormField extends FormField
{
    public static function create()
    {
        $args = func_get_args();
        $name = array_shift($args);
        $label = count($args) ? array_shift($args) : $name;
        $value = count($args) ? array_shift($args) : null;
        return parent::create(compact('name', 'label', 'value'));
    }

    public function __toString()
    {
        return '<div>' . $this->getError() . "</div><label for=\"{$this->name}\">{$this->label}</label><input type=\"text\" id=\"{$this->name}\" name=\"{$this->name}\" value=\"{$this->value}\">";
    }
}