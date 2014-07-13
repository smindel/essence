<?php

class SubmitFormField extends FormField
{
    public function html()
    {
        return "<div class=\"field " . get_class($this) . "\"><input type=\"" . $this->getHtmlType() . "\" name=\"" . $this->getFullName() . "\" value=\"{$this->label}\"></div>";
    }
}