<?php

class Authentication extends Controller
{
    public function challenge()
    {
        // aDebug(BASE_PATH, RELATIVE_SEGMENT, BASE_URL);die();

        $_SESSION['authentication_redirect'] = Controller::curr()->getRequest()->getUri();
        $this->redirect('Authentication/login');
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
        $form = Form::create('login', array(
            SecurityTokenFormField::create('SecurityID'),
            TextFormField::create('name'),
            PasswordFormField::create('password'),
            SubmitFormField::create('loginform_login', 'login'),
        ), $this);

        return array(
            'Form' => $form->handleRequest($this->request),
        );
    }

    public function loginform_login(Form $form)
    {
        $data = $form->getData();
        $user = User::one('name', $data['name']);
        $valid = $user && $data['password'] == $user->password;
        $fields = $form->getFields();

        if ($user && $valid) {
            $this->login($user);
            $this->redirect($_SESSION['authentication_redirect']);
        } else {
            $fields['name']->setError('Login or password is wrong');
        }
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