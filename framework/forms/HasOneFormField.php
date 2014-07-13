<?php

class HasOneFormField extends FormField
{
    public function html()
    {
        $name = $this->name;
        $options = '';
        foreach ($this->form->getObject()->$name() as $option) {
            $options .= "<div class=\"option\"><input name=\"" . $this->getFullName() . "\" id=\"{$this->name}[{$option->id}]\" type=\"radio\" value=\"{$option->id}\"" . ($option->id == $this->value ? ' checked' : '') . "> <a href=\"" . $option->link() . "\">{$option->title()}</a></div>";
        }
        return '<div class="field ' . get_class($this) . '"><div class="error">' . $this->getError() . "</div><label for=\"{$this->name}\">{$this->label}</label>{$options}</div>";
    }

    public function validate($value)
    {
        list(,,$constrain) = explode(':', $this->form->getObject()->db('type')[$this->name] . '::');
        if ($constrain == 'CASCADE' || $constrain == 'RESTRICT') {
            if ((int)$value < 1) return false;
        }
        return parent::validate($value);
    }
}