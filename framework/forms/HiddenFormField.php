<?php

class HiddenFormField extends FormField
{
    public function html()
    {
        return "<input type=\"" . $this->getHtmlType() . "\" id=\"{$this->name}\" name=\"" . $this->getFullName() . "\" value=\"{$this->value}\">";
    }
}