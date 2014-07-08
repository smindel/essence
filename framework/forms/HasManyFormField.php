<?php

class HasManyFormField extends FormField
{
    public function html()
    {
        $linkbase = get_class(Controller::curr()) . DIRECTORY_SEPARATOR . Controller::curr()->getRequest()->getMethodname() . DIRECTORY_SEPARATOR;
        $name = $this->name;
        $object = $this->form->getObject();
        list(,,$remotefield) = explode(':', $object->db('type')[$name]);
        $options = '';
        foreach ($object->$name() as $option) {
            $options .= "<div class=\"option\"><input name=\"{$this->name}[{$option->id}]\" id=\"{$this->name}[{$option->id}]\" type=\"checkbox\" value=\"{$option->id}\"" . ($option->$remotefield->id == $object->id ? ' checked' : '') . " disabled=\"\"> <a href=\"" . $linkbase . get_class($option) . DIRECTORY_SEPARATOR . $option->id . "\">{$option->title()}</a></div>";
        }
        return '<div class="field ' . get_class($this) . '"><div class="error">' . $this->getError() . "</div><label for=\"{$this->name}\">{$this->label}</label>{$options}</div>";
    }
}