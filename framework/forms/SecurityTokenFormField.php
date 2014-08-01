<?php

class SecurityTokenFormField extends HiddenFormField
{
    public function securityId()
    {
        if (isset($_SESSION['SecurityID'])) {
            return $_SESSION['SecurityID'];
        } else {
            return $_SESSION['SecurityID'] = sha1(uniqid());
        }
    }

    public function validate($value)
    {
        return isset($_SESSION['SecurityID']) && $_SESSION['SecurityID'] == $value && parent::validate($value);
    }
}