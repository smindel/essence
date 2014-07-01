<?php

class HasOneFormField extends FormField
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
        $options = '';
        foreach ($this->form->object->options($this->name) as $option) {
            $options .= "<option value=\"{$option->id}\"" . ($option->id == $this->value ? ' selected' : '') . ">{$option->title()}</option>";
        }
        return '<div class="field ' . get_class($this) . '"><div>' . $this->getError() . "</div><label for=\"{$this->name}\">{$this->label}</label><select id=\"{$this->name}\" name=\"{$this->name}\">{$options}</select></div>";
    }
}