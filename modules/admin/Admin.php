<?php

class Admin extends Controller
{
    public static $managed_models = array();

    public function beforeHandle($request)
    {
        if (!empty($_SESSION['user']) || in_array($request->methodname, array('login', 'loginform'))) return;
        $_SESSION['login_redirect'] = $_SERVER['REQUEST_URI'];
        $this->redirect($this->link('login'));
    }

    public function login_action() {
        return array(
            'Form' => $this->loginform_action(),
        );
    }

    public function loginform_action()
    {
        $form = Form::create(array(
            SecurityTokenFormField::create(),
            TextFormField::create('Name'),
            PasswordFormField::create('Password'),
            SubmitFormField::create('loginform_login', 'login'),
        ));
        return $form->setData($_REQUEST);
    }

    public function loginform_login(Form $form)
    {
        $data = $form->getData();
        $user = User::one('Name', $data['Name']);
        $valid = $data['Password'] == $user->Password;
        
        if ($user && $valid) {
            $_SESSION['user'] = $user->id;
        } else {
            $form->fields[1]->setError('Login oder Passwort falsch');
            $this->redirectBack();
        }
        $this->redirect($_SESSION['login_redirect']);
    }

    public function logout_action()
    {
        unset($_SESSION['user']);
        $this->redirectBack();
    }

    public function index_action() {
        $links = array();
        foreach (self::$managed_models as $model) {
            $links[$this->link('list', $model)] = $model;
        }
        return array(
            'Me' => $this,
            'Links' => $links,
        );
    }

    public function list_action() {
        $model = func_get_arg(0);
        $links = array(array(
            'link' => $this->link('edit', $model),
            'title' => "{$model} erstellen",
            'class' => 'create',
        ));
        foreach ($model::get() as $object) {
            $links[] = array(
                'link' => $this->link('edit', get_class($object), $object->id),
                'title' => $object->title(),
                'class' => 'edit',
            );
        }
        return array(
            'Model' => $model,
            'Me' => $this,
            'Links' => $links,
        );
    }

    public function edit_action() {
        $model = func_get_arg(0);
        $id = func_num_args() > 1 ? func_get_arg(1) : 0;
        return array(
            'Me' => $this,
            'Form' => $this->form_action($model, $id),
        );
    }

    public function form_action($model, $id = null)
    {
        $object = $model::one((int)$id) ?: $model::create();
        $fields = $object->getFields();
        $form = Form::create($fields)->setData($_REQUEST);
        // load data from object, then merge in data from previous submission
        return $form;
    }

    public function form_save(Form $form)
    {
        $object = $form->getObject();
        $object->hydrate($form->getData())->write();
        $this->redirect($this->link('edit', get_class($object), $object->id));
    }

    public function form_delete(Form $form)
    {
        $object = $form->object;
        $modelclass = get_class($object);
        $object->delete();
        $this->redirect($this->link('index', $modelclass));
    }
}