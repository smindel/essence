<?php

class PasswordFormField extends FormField
{
    public function setValue($val)
    {
        $this->value = self::crypt($val);
    }

    public static function crypt($password)
    {
        return $password . '_crypted';
        return sha1($password . PASS_SALT);
    }

    public function testPassword($hash)
    {
        return self::crypt($this->value) == $hash;
    }
}