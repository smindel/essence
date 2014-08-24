<?php

class Membership extends Model
{
    protected $db = array(
        'id' => array('type' => 'ID'),
        'group' => array('type' => 'FOREIGN', 'remoteclass' => 'Group', 'oninvalid' => 'CASCADE', 'required' => true),
        'user' => array('type' => 'FOREIGN', 'remoteclass' => 'User', 'oninvalid' => 'CASCADE', 'required' => true),
    );

    public function getTitle()
    {
        if ($this->id) return "{$this->group->title} > {$this->user->title}";
        return parent::getTitle();
    }
}