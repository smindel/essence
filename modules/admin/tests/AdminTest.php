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
                'tag' => 'input',
                'attributes' => array(
                    'type' => 'password',
                )
            ),
            $response
        );

        Authentication::create()->login(User::create()->hydrate(array('name' => 'Andy', 'password' => 'secret'))->write());

        $response = Request::create('Backend')->handle();

        $this->assertTag(
            array(
                'tag' => 'h1',
                'content' => 'AdminTest_Manager > AdminTest_Model1',
                'attributes' => array(
                    'class' => 'form-title',
                )
            ),
            $response
        );
    }

    public function testBaseFormCrud()
    {

        Authentication::create()->login(User::create()->hydrate(array('name' => 'Andy', 'password' => 'secret'))->write());

        $response = Request::create('Backend/panel/AdminTest_Manager/edit/AdminTest_Model1/0')->handle();

        $this->assertTag(
            array(
                'tag' => 'input',
                'attributes' => array(
                    'name' => 'AdminTest_Model1Form[name]',
                )
            ),
            $response,
            'Base form contains name field.'
        );

        $response = Request::create('Backend/panel/AdminTest_Manager/edit/AdminTest_Model1/0', array('AdminTest_Model1Form' => array(
            'name' => 'Sam',
            'form_save' => 'save',
        )))->handle();

        $this->assertTag(
            array(
                'tag' => 'div',
                'content' => 'An error occurred',
                'attributes' => array(
                    'class' => 'message error',
                )
            ),
            $response,
            'Invalid request due to missing SecurityToken'
        );

        $response = Request::create('Backend/panel/AdminTest_Manager/edit/AdminTest_Model1/0', array(
            'IgnoreSecurityToken' => true,
            'AdminTest_Model1Form' => array(
                'name' => 'Sam',
                'form_save' => 'save',
            )
        ))->handle();

        $this->assertTag(
            array(
                'tag' => 'input',
                'attributes' => array(
                    'name' => 'AdminTest_Model1Form[id]',
                    'value' => '1',
                )
            ),
            $response,
            'Form contains hidden id field with value set after saving'
        );

        $this->assertTag(
            array(
                'tag' => 'input',
                'attributes' => array(
                    'name' => 'AdminTest_Model1Form[name]',
                    'value' => 'Sam',
                )
            ),
            $response,
            'Form contains saved value'
        );

        $response = Request::create('Backend/panel/AdminTest_Manager/edit/AdminTest_Model1/1', array(
            'IgnoreSecurityToken' => true,
            'AdminTest_Model1Form' => array(
                'name' => 'Sam',
                'form_delete' => 'save',
            )
        ))->handle();

        $this->assertNull(AdminTest_Model1::one(1), 'record gets deleted.');

        $this->assertTag(
            array(
                'tag' => 'h1',
                'content' => 'AdminTest_Manager > AdminTest_Model1',
                'attributes' => array(
                    'class' => 'form-title',
                )
            ),
            $response,
            'UI returns to list view'
        );

    }

    public function testNestedFormCrud()
    {
        Authentication::create()->login(User::create()->hydrate(array('name' => 'Andy', 'password' => 'secret'))->write());

        $parent = AdminTest_Model1::create()->hydrate(array('name' => 'andy'))->write();

        $response = Request::create('Backend/panel/AdminTest_Manager/edit/AdminTest_Model1/' . $parent->id . '/fields/hasmany/edit/0')->handle();

        $this->assertTag(
            array(
                'tag' => 'a',
                'content' => 'andy',
                'attributes' => array(
                    'href' => 'http://localhost/Backend/panel/AdminTest_Manager/edit/AdminTest_Model1/1/',
                )
            ),
            $response,
            'form contains breadcrumb'
        );

        $this->assertTag(
            array(
                'tag' => 'input',
                'attributes' => array(
                    'name' => 'hasmanyForm[name]',
                )
            ),
            $response,
            'form contains childs name field'
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
        'name' => array('type' => 'TEXT'),
        'hasmany' => array('type' => 'LOOKUP', 'remoteclass' => 'AdminTest_Model2', 'remotefield' => 'hasone'),
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
        'name' => array('type' => 'TEXT'),
        'hasone' => array('type' => 'FOREIGN', 'remoteclass' => 'AdminTest_Model1', 'oninvalid' => 'RESTRICT'),
    );

    public function getFields()
    {
        $fields = parent::getFields();
        if (Controller::curr()->getRequest()->getRaw('IgnoreSecurityToken')) unset($fields['SecurityID']);
        return $fields;
    }
}