<?php

class AppTest extends PHPUnit_Framework_TestCase
{
    public function testConstants()
    {
        $this->assertEquals(BASE_PATH, '/Users/andy/Sites/interna', 'BASE_PATH correct');
        $this->assertEquals(BASE_URL, 'http://localhost/', 'BASE_URL correct');
    }
}