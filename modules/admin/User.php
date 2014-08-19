<?php

class User extends Model
{
    protected $db = array(
        'id' => array('type' => 'ID'),
        'name' => array('type' => 'TEXT', 'UNIQUE' => true, 'required' => true),
        'email' => array('type' => 'TEXT', 'field' => 'EmailFormField'),
        'password' => array('type' => 'TEXT', 'field' => 'PasswordFormField', 'label' => 'Passwort', 'required' => true),
    );

    public function title()
    {
        return $this->Name;
    }
}