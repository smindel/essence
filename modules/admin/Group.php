<?php

class Group extends Model
{
    protected $db = array(
        'id' => array('type' => 'ID'),
        'title' => array('type' => 'TEXT', 'required' => true),
        'group' => array('type' => 'FOREIGN', 'remoteclass' => 'Group', 'oninvalid' => 'RESTRICT'),
        'users' => array('type' => 'LOOKUP', 'remoteclass' => 'Membership', 'remotefield' => 'Group'),
    );
}