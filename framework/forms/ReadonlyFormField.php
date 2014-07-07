<?php

class ReadonlyFormField extends TextFormField
{
    public function __toString()
    {
        return '<div class="field ' . get_class($this) . '"><div>' . $this->getError() . "</div><label for=\"{$this->name}\">{$this->label}</label><span id=\"{$this->name}\">{$this->value}</span></div>";
    }
}