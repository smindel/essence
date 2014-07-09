<?php

class Authentication extends Controller
{
    public static function challenge()
    {
        $_SESSION['authentication_redirect'] = $_SERVER['REQUEST_URI'];
        $auth = self::create();
        $auth->redirect($auth->link('login'));
    }

    public function login(User $user)
    {
        $_SESSION['user'] = $user->id;
    }

    public function logout()
    {
        unset($_SESSION['user']);
    }

    public static function user()
    {
        if (isset($_SESSION['user'])) return User::one($_SESSION['user']);
    }

    public function login_action() {
        return array(
            'Form' => $this->loginform_action(),
        );
    }

    public function loginform_action()
    {
        $form = Form::create(array(
            SecurityTokenFormField::create('SecurityID'),
            TextFormField::create('Name'),
            PasswordFormField::create('Password'),
            SubmitFormField::create('loginform_login', 'login'),
        ), $this, __FUNCTION__);
        return $form->setData($_REQUEST);
    }

    public function loginform_login(Form $form)
    {
        $data = $form->getData();
        $user = User::one('Name', $data['Name']);
        $valid = $data['Password'] == $user->Password;
        $fields = $form->getFields();

        if ($user && $valid) {
            $this->login($user);
        } else {
            $fields['Name']->setError('Login oder Passwort falsch');
            $this->redirectBack();
        }
        $this->redirect($_SESSION['authentication_redirect']);
    }

    public function logout_action()
    {
        if (($user = self::user())) $this->logout($user);
        if (empty($_SERVER['HTTP_REFERER']) || Request::absolute_url($_SERVER['HTTP_REFERER']) == Request::absolute_url($_SERVER['REQUEST_URI'])) {
            $this->redirect($this->link('login'));
        } else {
            $this->redirectBack();
        }
    }
}