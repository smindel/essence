<?php

class HiddenFormField extends FormField
{
    public function html()
    {
        return "<input type=\"" . $this->getHtmlType() . "\" id=\"{$this->name}\" name=\"{$this->name}\" value=\"{$this->value}\">";
    }
}