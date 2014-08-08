<?php

class User extends Model
{
    protected $db = array(
        'id' => array('type' => 'ID'),
        'Name' => array('type' => 'TEXT'),
        'Email' => array('type' => 'TEXT', 'field' => 'EmailFormField'),
        'Password' => array('type' => 'TEXT', 'field' => 'PasswordFormField', 'label' => 'Passwort'),
    );

    public function title()
    {
        return $this->Name;
    }
}