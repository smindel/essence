<?php

class FormTest extends PHPUnit_Framework_TestCase
{
    public $db;
    public function setUp()
    {
        $this->db = Database::conn();
        Database::conn(new PDO('sqlite::memory:'));

        Builder::create()->build('FormTest_Model');
        Builder::create()->build('FormTest_ModelChild');
    }

    public function tearDown()
    {
        Database::conn($this->db);
    }

    public function testFormDisplay()
    {
        $obj = FormTest_Model::create();
        $obj->name = 'Andy';
        $obj->write();

        $controllerclass = 'FormTest_Controller';
        $methodname = 'edit';
        $param = $obj->id;

        $request = Request::create(implode('/', array($controllerclass, $methodname, $param)), array());
        $controller = Base::create($request->consume());
        $response = $controller->handleRequest($request);

        // Form tag
        $this->assertTag(
            array(
                'tag' => 'form',
                'attributes' => array(
                    // 'action' => 'name',
                )
            ),
            $response
        );

        // SecurityID setup correctly
        $this->assertTag(
            array(
                'tag' => 'input',
                'attributes' => array(
                    'id' => 'SecurityID',
                    'name' => 'FormTest_ModelForm[SecurityID]',
                    'type' => 'hidden',
                )
            ),
            $response
        );

        // Property fields setup correctly
        $this->assertTag(
            array(
                'tag' => 'input',
                'attributes' => array(
                    'id' => 'name',
                    'name' => 'FormTest_ModelForm[name]',
                    'type' => 'text',
                    'value' => 'Andy',
                )
            ),
            $response
        );

        // Submit button exists
        $this->assertTag(
            array(
                'tag' => 'button',
                'attributes' => array(
                    'name' => 'FormTest_ModelForm[form_save]',
                    'type' => 'submit',
                )
            ),
            $response
        );

        // delete button exists
        $this->assertTag(
            array(
                'tag' => 'button',
                'attributes' => array(
                    'name' => 'FormTest_ModelForm[form_delete]',
                    'type' => 'submit',
                )
            ),
            $response
        );
    }

    public function testFormSubmit()
    {
        $controllerclass = 'FormTest_Controller';
        $methodname = 'edit';

        $data = array(
            'IgnoreSecurityToken' => true,
            'FormTest_ModelForm' => array('name' => 'Andy', 'form_save' => 'save')
        );
        $request = Request::create(implode('/', array($controllerclass, $methodname)), $data);
        $controller = Base::create($request->consume());
        $response = $controller->handleRequest($request);

        $this->assertEquals('Andy', FormTest_Model::one()->name, 'Form submission to create new object');



        $controllerclass = 'FormTest_Controller';
        $methodname = 'edit';
        $param = FormTest_Model::one()->id;

        $data = array(
            'IgnoreSecurityToken' => true,
            'FormTest_ModelForm' => array('name' => 'Christian', 'form_save' => 'save')
        );
        $request = Request::create(implode('/', array($controllerclass, $methodname, $param)), $data);
        $controller = Base::create($request->consume());
        $response = $controller->handleRequest($request);

        $this->assertEquals('Christian', FormTest_Model::one()->name, 'Form submission succeeds without Security Token');



        $controllerclass = 'FormTest_Controller';
        $methodname = 'edit';
        $param = FormTest_Model::one()->id;

        $data = array(
            'IgnoreSecurityToken' => false,
            'FormTest_ModelForm' => array('name' => 'Tom', 'form_save' => 'save')
        );
        $request = Request::create(implode('/', array($controllerclass, $methodname, $param)), $data);
        $controller = Base::create($request->consume());
        $response = $controller->handleRequest($request);

        $this->assertEquals('Christian', FormTest_Model::one()->name, 'Form submission fails with empty Security Token');

        $controllerclass = 'FormTest_Controller';
        $methodname = 'edit';
        $param = FormTest_Model::one()->id;

        $data = array(
            'IgnoreSecurityToken' => true,
            'FormTest_ModelForm' => array('form_delete' => 'delete')
        );
        $request = Request::create(implode('/', array($controllerclass, $methodname, $param)), $data);
        $controller = Base::create($request->consume());
        $response = $controller->handleRequest($request);

        $this->assertNull(FormTest_Model::one(), 'Form deletes record');
    }

    public function testNestedFormSubmit()
    {
        $parent = FormTest_Model::create();
        $parent->name = 'Andy';
        $parent->write();

        $child = FormTest_ModelChild::create();
        $child->name = 'Milly';
        $child->daddy = $parent;
        $child->write();

        $controllerclass = 'FormTest_Controller';
        $methodname = 'edit';

        $data = array(
            'IgnoreSecurityToken' => true,
            'childrenForm' => array('name' => 'Yoko', 'daddy' => $parent->id, 'form_save' => 'save')
        );
        $request = Request::create(implode('/', array($controllerclass, $methodname, $parent->id, 'fields', 'children', $methodname, $child->id)), $data);
        $controller = Base::create($request->consume());
        $response = $controller->handleRequest($request);

        $this->assertEquals('Yoko', FormTest_ModelChild::one($child->id)->name, 'Nested Form can handle submission');

        $controllerclass = 'FormTest_Controller';
        $methodname = 'edit';

        $data = array(
            'IgnoreSecurityToken' => true,
            'childrenForm' => array('name' => 'Yoko', 'daddy' => $parent->id, 'form_delete' => 'delete')
        );
        $request = Request::create(implode('/', array($controllerclass, $methodname, $parent->id, 'fields', 'children', $methodname, $child->id)), $data);
        $controller = Base::create($request->consume());
        $response = $controller->handleRequest($request);

        $this->assertNull(FormTest_ModelChild::one($child->id), 'Nested Form can delete record');
    }
}

class FormTest_Controller extends Controller
{
    protected $object;

    public function getObject()
    {
        return $this->object;
    }

    public function getForm($id)
    {
        $this->object = FormTest_Model::one($id) ?: FormTest_Model::create();
        $fields = $this->object->getFields();

        return Form::create('FormTest_ModelForm', $fields, $this);
    }

    public function index_action() {

        return array(
            'List' => 'List',
        );
    }

    public function edit_action($id = null) {

        return array(
            'Form' => $this->getForm($id)->handleRequest($this->request),
        );
    }

    public function form_save(Form $form)
    {
        $this->object->hydrate($form->getData())->write();
        $this->redirect($this->link('edit', $this->object->id));
    }

    public function form_delete(Form $form)
    {
        $this->object->delete();
        $this->redirect($this->link('index'));
    }
}

class FormTest_Model extends Model
{
    protected $db = array(
        'id' => array('type' => 'ID'),
        'name' => array('type' => 'TEXT'),
        'children' => array('type' => 'LOOKUP', 'remoteclass' => 'FormTest_ModelChild', 'remotefield' => 'daddy'),
    );
}

class FormTest_ModelChild extends Model
{
    protected $db = array(
        'id' => array('type' => 'ID'),
        'name' => array('type' => 'TEXT'),
        'daddy' => array('type' => 'FOREIGN', 'remoteclass' => 'FormTest_Model', 'oninvalid' => 'RESTRICT'),
    );

    public function getFields()
    {
        $fields = parent::getFields();
        if (Controller::curr()->getRequest()->getRaw('IgnoreSecurityToken')) unset($fields['SecurityID']);
        return $fields;
    }
}