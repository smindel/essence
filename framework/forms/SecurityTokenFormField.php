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
        if (
            (ENV_TYPE == 'test' && Controller::curr()->getRequest()->getRaw('IgnoreSecurityToken')) ||
            (isset($_SESSION['SecurityID']) && $_SESSION['SecurityID'] == $value)
        ) return parent::validate($value);

        $this->setError('An error occurred. Please try again.');
        return false;
    }
}