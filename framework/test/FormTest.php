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
                'tag' => 'input',
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
                'tag' => 'input',
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
    }
}

class FormTest_Controller extends Controller
{
    public function edit_action($id = null) {
        $this->object = FormTest_Model::one($id) ?: FormTest_Model::create();
        $fields = $this->object->getFields();

        if ($this->request->getRaw('IgnoreSecurityToken')) unset($fields['SecurityID']);

        $form = Form::create('FormTest_ModelForm', $fields, $this);
        $form->setAction($this->link('edit', 'FormTest_Model', $id));

        return array(
            'Form' => $form->handleRequest($this->request),
        );
    }

    public function form_save(Form $form)
    {
        $this->object->hydrate($form->getData())->write();
        $this->redirect($this->link('edit', get_class($this->object), $this->object->id));
    }

    public function form_delete(Form $form)
    {
        $this->object->delete();
        $this->redirect($this->link('index', get_class($this->object)));
    }
}

class FormTest_Model extends Model
{
    protected $db = array(
        'id' => array('type' => 'ID'),
        'Name' => array(),
    );
}