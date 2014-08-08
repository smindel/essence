<?php

class HasOneFormField extends RelationFormField
{
    public function validate($value)
    {
        $constrain = $this->parent->getObject()->getProperty($this->name, 'oninvalid');
        if ($constrain == 'CASCADE' || $constrain == 'RESTRICT') {
            if ((int)$value < 1) return false;
        }
        return parent::validate($value);
    }
}