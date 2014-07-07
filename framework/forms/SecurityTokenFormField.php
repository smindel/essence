<?php

class SecurityTokenFormField extends HiddenFormField
{
    public function __toString()
    {
        return "<input type=\"hidden\" id=\"{$this->name}\" name=\"{$this->name}\" value=\"" . $this->securityId() . "\">";
    }

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