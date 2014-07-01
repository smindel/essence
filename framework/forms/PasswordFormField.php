<?php

class PasswordFormField extends FormField
{
    public static function create()
    {
        $args = func_get_args();
        $name = array_shift($args);
        $label = count($args) ? array_shift($args) : $name;
        $value = count($args) ? array_shift($args) : null;
        return parent::create(compact('name', 'label', 'value'));
    }

    public function __get($key)
    {
        if ($key != 'value') return parent::__get($key);
        return $this->dependencies['value'];
    }

    public function __set($key, $val)
    {
        if ($key != 'value') return parent::__set($key, $val);
        $this->dependencies['value'] = self::crypt($val);
    }

    public function __toString()
    {
        return '<div class="field ' . get_class($this) . '"><div>' . $this->getError() . "</div><label for=\"{$this->name}\">{$this->label}</label><input type=\"password\" id=\"{$this->name}\" name=\"{$this->name}\" value=\"\"></div>";
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

// transparently mimic crypt() process
function crypty($input, $salt = null)
{
    return $input . '_crypty';
}