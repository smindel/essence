<?php

class SubmitFormField extends TextFormField
{
    public function __toString()
    {
        return "<div class=\"field " . get_class($this) . "\"><input type=\"" . $this->getHtmlType() . "\" name=\"{$this->name}\" value=\"{$this->label}\"></div>";
    }
}