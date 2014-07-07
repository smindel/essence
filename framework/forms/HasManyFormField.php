<?php

class HasManyFormField extends FormField
{
    public function __toString()
    {
        try {
            $object = $this->form->getObject();
            $options = $object->options($this->name);
            $field = $object->option($this->name);
            $optionsstring = '';
            foreach ($options as $option) {
                $optionsstring .= "<option value=\"{$option->id}\"" . ($option->$field == $object->id ? ' selected' : '') . ">{$option->title()}</option>";
            }
            return '<div class="field ' . get_class($this) . '"><div>' . $this->getError() . "</div><label for=\"{$this->name}\">{$this->label}</label><select multiple id=\"{$this->name}\" name=\"{$this->name}\">{$optionsstring}</select></div>";
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}