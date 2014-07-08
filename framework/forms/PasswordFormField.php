<?php

class PasswordFormField extends FormField
{
    public function setValue($val)
    {
        $this->value = self::crypt($val);
    }

    public function html()
    {
        return '<div class="field ' . get_class($this) . '"><div class="error">' . $this->getError() . "</div><label for=\"{$this->name}\">{$this->label}</label><input type=\"" . $this->getHtmlType() . "\" id=\"{$this->name}\" name=\"{$this->name}\" value=\"\"></div>";
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