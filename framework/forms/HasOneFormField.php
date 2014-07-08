<?php

class HasOneFormField extends FormField
{
    public function __toString()
    {
        $name = 
        $options = '';
        foreach ($this->form->getObject()->$name as $option) {
            $options .= "<option value=\"{$option->id}\"" . ($option->id == $this->value ? ' selected' : '') . ">{$option->title()}</option>";
        }
        return '<div class="field ' . get_class($this) . '"><div>' . $this->getError() . "</div><label for=\"{$this->name}\">{$this->label}</label><select id=\"{$this->name}\" name=\"{$this->name}\">{$options}</select></div>";
    }
}