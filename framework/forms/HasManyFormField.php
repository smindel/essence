<?php

class HasManyFormField extends FormField
{
    public function __toString()
    {
        try {
            $name = $this->name;
            $object = $this->form->getObject();
            list(,,$remotefield) = explode(':', $object->db('type')[$name]);
            $optionsstring = '';
            foreach ($object->$name() as $option) {
                $optionsstring .= "<option value=\"{$option->id}\"" . ($option->$remotefield->id == $object->id ? ' selected' : '') . ">{$option->title()}</option>";
            }
            return '<div class="field ' . get_class($this) . '"><div>' . $this->getError() . "</div><label for=\"{$this->name}\">{$this->label}</label><select multiple id=\"{$this->name}\" name=\"{$this->name}\">{$optionsstring}</select></div>";
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}