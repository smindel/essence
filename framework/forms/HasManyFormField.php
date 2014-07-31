<?php

class HasManyFormField extends FormField
{
    public function __toString()
    {
        $name = $this->name;
        $object = $this->form->getObject();
        list(,,$remotefield) = explode(':', $object->getProperty($name));
        $options = '';
        foreach ($object->$name() as $option) {
            $options .= "<div class=\"option\"><input name=\"" . $this->getFullName() . "[{$option->id}]\" id=\"{$this->name}[{$option->id}]\" type=\"checkbox\" value=\"{$option->id}\"" . ($option->$remotefield->id == $object->id ? ' checked' : '') . " disabled=\"\"> <a href=\"" . $option->link() . "\">{$option->title()}</a></div>";
        }
        return '<div class="field ' . get_class($this) . '"><div class="error">' . $this->getError() . "</div><label for=\"{$this->name}\">{$this->label}</label>{$options}</div>";
    }
}