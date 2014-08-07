<?php

class FormTest extends PHPUnit_Framework_TestCase
{
    public $db;
    public function setUp()
    {
        $this->db = Database::conn();
        Database::conn(new PDO('sqlite::memory:'));
    }

    public function tearDown()
    {
        Database::conn($this->db);
    }

    public function testFormDisplay()
    {
        Builder::create()->build('FormTest_Model');
        Builder::create()->build('FormTest_ModelChild');

        $obj = FormTest_Model::create();
        $obj->Name = 'Andy';
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
                    // 'action' => 'Name',
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
                    'id' => 'Name',
                    'name' => 'FormTest_ModelForm[Name]',
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
        Builder::create()->build('FormTest_Model');
        Builder::create()->build('FormTest_ModelChild');

        $controllerclass = 'FormTest_Controller';
        $methodname = 'edit';

        $data = array(
            'IgnoreSecurityToken' => true,
            'FormTest_ModelForm' => array('Name' => 'Andy', 'form_save' => 'save')
        );
        $request = Request::create(implode('/', array($controllerclass, $methodname)), $data);
        $controller = Base::create($request->consume());
        $response = $controller->handleRequest($request);

        $this->assertEquals('Andy', FormTest_Model::one()->Name, 'Form submission to create new object');



        $controllerclass = 'FormTest_Controller';
        $methodname = 'edit';
        $param = FormTest_Model::one()->id;

        $data = array(
            'IgnoreSecurityToken' => true,
            'FormTest_ModelForm' => array('Name' => 'Christian', 'form_save' => 'save')
        );
        $request = Request::create(implode('/', array($controllerclass, $methodname, $param)), $data);
        $controller = Base::create($request->consume());
        $response = $controller->handleRequest($request);

        $this->assertEquals('Christian', FormTest_Model::one()->Name, 'Form submission succeeds without Security Token');



        $controllerclass = 'FormTest_Controller';
        $methodname = 'edit';
        $param = FormTest_Model::one()->id;

        $data = array(
            'IgnoreSecurityToken' => false,
            'FormTest_ModelForm' => array('Name' => 'Tom', 'form_save' => 'save')
        );
        $request = Request::create(implode('/', array($controllerclass, $methodname, $param)), $data);
        $controller = Base::create($request->consume());
        $response = $controller->handleRequest($request);

        $this->assertEquals('Christian', FormTest_Model::one()->Name, 'Form submission fails with empty Security Token');

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
        Builder::create()->build('FormTest_Model');
        Builder::create()->build('FormTest_ModelChild');

        $parent = FormTest_Model::create();
        $parent->Name = 'Andy';
        $parent->write();

        $child = FormTest_ModelChild::create();
        $child->Name = 'Milly';
        $child->Daddy = $parent;
        $child->write();

        $controllerclass = 'FormTest_Controller';
        $methodname = 'edit';

        $data = array(
            'IgnoreSecurityToken' => true,
            'ChildrenForm' => array('Name' => 'Yoko', 'Daddy' => $parent->id, 'form_save' => 'save')
        );
        $request = Request::create(implode('/', array($controllerclass, $methodname, $parent->id, 'fields', 'Children', $methodname, $child->id)), $data);
        $controller = Base::create($request->consume());
        $response = $controller->handleRequest($request);

        $this->assertEquals('Yoko', FormTest_ModelChild::one($child->id)->Name, 'Nested Form can handle submission');

        $controllerclass = 'FormTest_Controller';
        $methodname = 'edit';

        $data = array(
            'IgnoreSecurityToken' => true,
            'ChildrenForm' => array('Name' => 'Yoko', 'Daddy' => $parent->id, 'form_delete' => 'delete')
        );
        $request = Request::create(implode('/', array($controllerclass, $methodname, $parent->id, 'fields', 'Children', $methodname, $child->id)), $data);
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

        if ($this->request->getRaw('IgnoreSecurityToken')) unset($fields['SecurityID']);

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
        'Name' => array(),
        'Children' => array('type' => 'LOOKUP:FormTest_ModelChild:Daddy'),
    );

    public function getFields()
    {
        $fields = parent::getFields();
        if (Controller::curr()->getRequest()->getRaw('IgnoreSecurityToken')) unset($fields['SecurityID']);
        return $fields;
    }
}

class FormTest_ModelChild extends Model
{
    protected $db = array(
        'id' => array('type' => 'ID'),
        'Name' => array(),
        'Daddy' => array('type' => 'FOREIGN:FormTest_Model:RESTRICT'),
    );

    public function getFields()
    {
        $fields = parent::getFields();
        if (Controller::curr()->getRequest()->getRaw('IgnoreSecurityToken')) unset($fields['SecurityID']);
        return $fields;
    }
}