<?php

class HasOneFormField extends RelationFormField
{
    public function validate($value)
    {
        list(,,$constrain) = explode(':', $this->parent->getObject()->getProperty($this->name) . '::');
        if ($constrain == 'CASCADE' || $constrain == 'RESTRICT') {
            if ((int)$value < 1) return false;
        }
        return parent::validate($value);
    }
}