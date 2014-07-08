<?php

class SubmitFormField extends FormField
{
    public function html()
    {
        return "<div class=\"field " . get_class($this) . "\"><input type=\"" . $this->getHtmlType() . "\" name=\"{$this->name}\" value=\"{$this->label}\"></div>";
    }
}