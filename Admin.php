<?php

class Admin extends Controller
{
    public function beforeHandle($request)
    {
        // $this->redirect('admin/login');
    }

    public function login_action() {}

    public function edit_action() {
        return array(
            'Form' => $this->form_action(),
        );
    }

    public function form_action()
    {
        return Form::create(array(
            SecurityTokenFormField::create(),
            TextFormField::create('name', 'Name', isset($_SESSION['name']) ? strtoupper($_SESSION['name']) : ''),
            EmailFormField::create('email', 'E-Mail', isset($_SESSION['email']) ? strtoupper($_SESSION['email']) : '')->setValidator(function($value){
                return !$value || preg_match('/.+@.+\..+/', $value);
            }),
            SubmitFormField::create('form_submit', 'GO !'),
        ));
    }

    public function form_submit(Form $form, array $data)
    {
        $_SESSION['name'] = $data['name'];
        $_SESSION['email'] = $data['email'];
        $this->redirectBack();
    }
}