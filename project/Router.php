<?php

class Router extends Controller
{
    public function index_action()
    {
        return $this->handleAction('bounce', func_get_args());
    }

    public function bounce_action()
    {
        $this->redirect('http://www.google.de/', 302);
    }
}