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

    public function testCRUD()
    {
        Builder::create()->build('TestModel');

        // create and write object
        $obj = TestModel::create();
        $obj->Name = 'Andy';
        $obj->write();

        // test write set id
        $id = $obj->id;
        $this->assertTrue($id > 0);

        // test read and correct property assignment
        $this->assertEquals(TestModel::one($id)->id, $id);
        $this->assertEquals(TestModel::one('id', $id)->Name, 'Andy');

        // change property
        $obj->Name = 'Sarah';
        $obj->write();
        $this->assertEquals(TestModel::one(array('id' => $id))->title(), 'Sarah');

        // delete object
        $obj->delete();
        $this->assertNull(TestModel::one($id));
    }
}

class TestModel extends Model
{
    protected $db = array(
        'id' => array('type' => 'ID'),
        'Name' => array(),
    );
}