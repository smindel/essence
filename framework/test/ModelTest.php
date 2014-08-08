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
        Builder::create()->build('ModelTest_Model');

        // create and write object
        $obj = ModelTest_Model::create();
        $obj->Name = 'Andy';
        $obj->write();

        // test write set id
        $id = $obj->id;
        $this->assertTrue($id > 0);

        // test read and correct property assignment
        $this->assertEquals(ModelTest_Model::one($id)->id, $id);
        $this->assertEquals(ModelTest_Model::one('id', $id)->Name, 'Andy');

        // change property
        $obj->Name = 'Sarah';
        $obj->write();
        $this->assertEquals(ModelTest_Model::one(array('id' => $id))->title(), 'Sarah');

        // delete object
        $obj->delete();
        $this->assertNull(ModelTest_Model::one($id));
    }

    public function testRealtions()
    {
        Builder::create()->build('ModelTest_Model');
        Builder::create()->build('ModelTest_ModelChild');

        $parent1 = ModelTest_Model::create();
        $parent1->Name = 'Andy';
        $parent1->write();

        $parent2 = ModelTest_Model::create();
        $parent2->Name = 'Robert';
        $parent2->write();

        $child1 = ModelTest_ModelChild::create();
        $child1->Name = 'Yoko';
        $child1->write();

        $child2 = ModelTest_ModelChild::create();
        $child2->Name = 'Milli';
        $child2->write();

        $child3 = ModelTest_ModelChild::create();
        $child3->Name = 'Paula';
        $child3->write();

        $this->assertEquals(3, $parent1->Children()->count());
        $this->assertEquals(2, $child1->Daddy()->count());
        $this->assertEquals(0, $parent1->Children->count());
        $this->assertNull($child1->Daddy);

        $child1->Daddy = $parent1;
        $child1->write();
        $child2->Daddy = $parent1;
        $child2->write();
        $child3->Daddy = $parent2;
        $child3->write();

        $this->assertEquals(2, $parent1->Children->count());
        $this->assertEquals('Andy', $child1->Daddy->Name);
        $this->assertEquals('Andy', $child2->Daddy->Name);
        $this->assertEquals(1, $parent2->Children->count());
        $this->assertEquals('Robert', $child3->Daddy->Name);
    }
}

class ModelTest_Model extends Model
{
    protected $db = array(
        'id' => array('type' => 'ID'),
        'Name' => array('type' => 'TEXT'),
        'Children' => array('type' => 'LOOKUP', 'remoteclass' => 'ModelTest_ModelChild', 'remotefield' => 'Daddy'),
    );
}

class ModelTest_ModelChild extends Model
{
    protected $db = array(
        'id' => array('type' => 'ID'),
        'Name' => array('type' => 'TEXT'),
        'Daddy' => array('type' => 'FOREIGN', 'remoteclass' => 'ModelTest_Model', 'oninvalid' => 'RESTRICT'),
    );
}