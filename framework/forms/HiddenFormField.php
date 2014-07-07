<?php

class HiddenFormField extends FormField
{
    public function __toString()
    {
        return "<input type=\"" . $this->getHtmlType() . "\" id=\"{$this->name}\" name=\"{$this->name}\" value=\"{$this->value}\">";
    }
}