<?php

class User extends Model
{
    protected $db = array(
        'id' => array('type' => 'ID'),
        'Name' => array('type' => 'TEXT', 'UNIQUE' => true, 'required' => true),
        'Email' => array('type' => 'TEXT', 'field' => 'EmailFormField'),
        'Password' => array('type' => 'TEXT', 'field' => 'PasswordFormField', 'label' => 'Passwort', 'required' => true),
    );

    public function title()
    {
        return $this->Name;
    }
}