<?php

class TextFormField extends FormField
{
    public function __toString()
    {
        return '<div class="field ' . get_class($this) . '"><div>' . $this->getError() . "</div><label for=\"{$this->name}\">{$this->label}</label><input type=\"" . $this->getHtmlType() . "\" id=\"{$this->name}\" name=\"{$this->name}\" value=\"{$this->value}\"></div>";
    }
}