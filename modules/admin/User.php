<?php

class User extends Model
{
    protected $db = array(
        'id' => array('type' => 'ID', 'field' => 'HiddenFormField'),
        'Name' => array(),
        'Email' => array('field' => 'EmailFormField'),
        'Password' => array('field' => 'PasswordFormField', 'label' => 'Passwort'),
    );

    public function title()
    {
        return $this->Name;
    }
}