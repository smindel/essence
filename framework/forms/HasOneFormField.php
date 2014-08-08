<?php

class HasOneFormField extends RelationFormField
{
    protected $oninvalid;

    public function __construct($name, $label = null, $value = null, $options = null, $class = null, $oninvalid = null)
    {
        parent::__construct($name, $label, $value);
        $this->options = $options;
        $this->class = $class;
        $this->oninvalid = $oninvalid;
    }

    public function getObject()
    {
        return isset($this->options[$this->value]) ? $this->options[$this->value] : null; // Base::create($this->getClass());
    }

    public function validate($value)
    {
        if ($this->oninvalid == 'CASCADE' || $this->oninvalid == 'RESTRICT') {
            if ((int)$value < 1) return false;
        }
        return parent::validate($value);
    }

    public function canSetNull()
    {
        return $this->oninvalid == 'SET NULL';
    }
}