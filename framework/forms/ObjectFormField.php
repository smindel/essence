<?php

class ObjectFormField extends ModelFormField
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
        if (($this->oninvalid == 'CASCADE' || $this->oninvalid == 'RESTRICT') && (int)$value < 1) {
            $this->setError('Please choose a value for this field.');
            return false;
        }
        return parent::validate($value);
    }

    public function canSetNull()
    {
        return $this->oninvalid == 'SET NULL';
    }
}