<?php

class ControllerTest extends PHPUnit_Framework_TestCase
{
    public function testRequestHandling()
    {
        $controllerclass = 'ControllerTest_Controller';
        $methodname = 'lowercase';
        $param = 'Param';

        $request = Request::create(implode('/', array($controllerclass, $methodname, $param)), array());
        $controller = Base::create($request->consume());
        $response = $controller->handleRequest($request);

        $this->assertEquals(strtolower($param), $response);
    }

    public function testNestedRequestHandling()
    {
        $controllerclass = 'ControllerTest_Controller';
        $methodname = 'forward';
        $param[] = 'ControllerTest_Controller';
        $param[] = 'uppercase';
        $param[] = 'Test';

        $request = Request::create(implode('/', array($controllerclass, $methodname, implode('/', $param))), array());
        $controller = Base::create($request->consume());
        $response = $controller->handleRequest($request);

        $this->assertEquals(strtoupper(array_pop($param)), $response);
    }
}

class ControllerTest_Controller extends Controller
{
    function lowercase_action($param)
    {
        return strtolower($param);
    }

    function uppercase_action($param)
    {
        return strtoupper($param);
    }

    function forward_action($controllerclass)
    {
        $subcontroller = $controllerclass::create($this);
        return $subcontroller->handleRequest($this->getRequest());
    }
}