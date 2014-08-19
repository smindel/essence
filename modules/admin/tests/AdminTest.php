<?php

class AdminTest extends PHPUnit_Framework_TestCase
{
    public $db;
    public function setUp()
    {
        $this->db = Database::conn();
        Database::conn(new PDO('sqlite::memory:'));

        Builder::create()->build('AdminTest_Model1');
        Builder::create()->build('AdminTest_Model2');
        Builder::create()->build('User');

        Backend::$managers = array('AdminTest_Manager');
    }

    public function tearDown()
    {
        Database::conn($this->db);
    }

    public function testAuthentication()
    {
        $response = Request::create('Backend')->handle();

        $this->assertTag(
            array(
                'tag' => 'h1',
                'attributes' => array(
                    'class' => 'form-title',
                )
            ),
            $response
        );

        Authentication::create()->login(User::create()->hydrate(array('name' => 'Andy', 'password' => 'secret'))->write());

        $response = Request::create('Backend')->handle();

        $this->assertTag(
            array(
                'tag' => 'h1',
                'attributes' => array(
                    'class' => 'form-title',
                )
            ),
            $response
        );
    }
}

class AdminTest_Manager extends Admin
{
    protected static $managed_models = array('AdminTest_Model1', 'AdminTest_Model2');
    protected static $icon = "";
}

class AdminTest_Model1 extends Model
{
    protected $db = array(
        'id' => array('type' => 'ID'),
        'Name' => array('type' => 'TEXT'),
        'HasMany' => array('type' => 'LOOKUP', 'remoteclass' => 'FormTest_ModelChild', 'remotefield' => 'Daddy'),
    );

    public function getFields()
    {
        $fields = parent::getFields();
        if (Controller::curr()->getRequest()->getRaw('IgnoreSecurityToken')) unset($fields['SecurityID']);
        return $fields;
    }
}

class AdminTest_Model2 extends Model
{
    protected $db = array(
        'id' => array('type' => 'ID'),
        'Name' => array('type' => 'TEXT'),
        'HasOne' => array('type' => 'FOREIGN', 'remoteclass' => 'FormTest_Model', 'oninvalid' => 'RESTRICT'),
    );

    public function getFields()
    {
        $fields = parent::getFields();
        if (Controller::curr()->getRequest()->getRaw('IgnoreSecurityToken')) unset($fields['SecurityID']);
        return $fields;
    }
}