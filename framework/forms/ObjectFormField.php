<?php

class ObjectFormField extends ModelFormField
{
    protected $oninvalid;

    public function __construct($name, $label = null, $value = null, $options = null, $class = null)
    {
        parent::__construct($name, $label, $value);
        $this->options = $options;
        $this->class = $class;
    }

    public function getObject()
    {
        return isset($this->options[$this->value]) ? $this->options[$this->value] : null;
    }
}