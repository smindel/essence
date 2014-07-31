<?php

class HiddenFormField extends FormField
{
    public function __toString()
    {
        return "<input type=\"" . $this->getHtmlType() . "\" id=\"{$this->name}\" name=\"" . $this->getFullName() . "\" value=\"{$this->value}\">";
    }
}