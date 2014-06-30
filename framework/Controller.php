<?php

abstract class Controller extends Base
{
    public function handleRequest($request)
    {
        if (method_exists($this, 'beforeHandle')) $this->beforeHandle($request);

        $data = $this->handleAction($request->methodname, $request->parameters);

        if (method_exists($this, 'afterRender')) $data = $this->afterRender($data);

        echo $data;
    }

    public function handleAction($method, $parameters)
    {
        $data = call_user_func_array(array($this, $method . '_action'), $parameters);
        if (!is_string($data)) {
            $data = $this->render($method, $data);
        }
        return $data;
    }

    public function render($method, $data)
    {
        return View::create(get_class($this), $method)->render($data);
    }

    public function redirect($url, $code = 302)
    {
        header('Location: ' . $url, true, $code);
        exit;
    }

    public function redirectBack()
    {
        $this->redirect($_SERVER['HTTP_REFERER']);
    }

    public function link()
    {
        return BASE_URL . get_class($this) . '/' . implode('/', func_get_args());
    }
}