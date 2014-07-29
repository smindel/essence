<?php

class ModelTest extends PHPUnit_Framework_TestCase
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

    public function testTest()
    {
        Builder::create()->build('TestModel');
        $obj = TestModel::create();
        $obj->Name = 'Andy';
        $obj->write();
        $id = $obj->id;
        $this->assertTrue($id > 0);
        $this->assertEquals(TestModel::one($id)->id, $id);
    }
}

class TestModel extends Model
{
    protected $db = array(
        'id' => array('type' => 'ID'),
        'Name' => array(),
    );
}