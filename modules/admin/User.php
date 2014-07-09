<?php

class User extends Model
{
    protected $db = array(
        'id' => array('type' => 'ID', 'field' => 'HiddenFormField'),
        'Name' => array('field' => 'TextFormField'),
        'Email' => array('field' => 'EmailFormField'),
        'Password' => array('field' => 'PasswordFormField', 'label' => 'Passwort'),
    );

    public function title()
    {
        return $this->Name;
    }

    public function login()
    {
        $_SESSION['user'] = $this->id;
    }

    public function logout()
    {
        unset($_SESSION['user']);
    }

    public static function curr()
    {
        if (isset($_SESSION['user'])) return self::one($_SESSION['user']);
    }
}