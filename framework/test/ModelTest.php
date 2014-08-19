<?php

class ModelTest extends PHPUnit_Framework_TestCase
{
    public $db;
    public function setUp()
    {
        $this->db = Database::conn();
        Database::conn(new PDO('sqlite::memory:'));

        Builder::create()->build('ModelTest_Model');
        Builder::create()->build('ModelTest_ModelChild');
    }

    public function tearDown()
    {
        Database::conn($this->db);
    }

    public function testCRUD()
    {
        // create and write object
        $obj = ModelTest_Model::create();
        $obj->name = 'Andy';
        $obj->write();

        // test write set id
        $id = $obj->id;
        $this->assertTrue($id > 0);

        // test read and correct property assignment
        $this->assertEquals(ModelTest_Model::one($id)->id, $id);
        $this->assertEquals(ModelTest_Model::one('id', $id)->name, 'Andy');

        // change property
        $obj->name = 'Sarah';
        $obj->write();
        $this->assertEquals(ModelTest_Model::one(array('id' => $id))->getTitle(), 'Sarah');

        // delete object
        $obj->delete();
        $this->assertNull(ModelTest_Model::one($id));
    }

    public function testRealtions()
    {
        $parent1 = ModelTest_Model::create();
        $parent1->name = 'Andy';
        $parent1->write();

        $parent2 = ModelTest_Model::create();
        $parent2->name = 'Robert';
        $parent2->write();

        $child1 = ModelTest_ModelChild::create();
        $child1->name = 'Yoko';
        $child1->write();

        $child2 = ModelTest_ModelChild::create();
        $child2->name = 'Milli';
        $child2->write();

        $child3 = ModelTest_ModelChild::create();
        $child3->name = 'Paula';
        $child3->write();

        $this->assertEquals(3, $parent1->children()->count());
        $this->assertEquals(2, $child1->daddy()->count());
        $this->assertEquals(0, $parent1->children->count());
        $this->assertNull($child1->daddy);

        $child1->daddy = $parent1;
        $child1->write();
        $child2->daddy = $parent1;
        $child2->write();
        $child3->daddy = $parent2;
        $child3->write();

        $this->assertEquals(2, $parent1->children->count());
        $this->assertEquals('Andy', $child1->daddy->name);
        $this->assertEquals('Andy', $child2->daddy->name);
        $this->assertEquals(1, $parent2->children->count());
        $this->assertEquals('Robert', $child3->daddy->name);
    }
}

class ModelTest_Model extends Model
{
    protected $db = array(
        'id' => array('type' => 'ID'),
        'name' => array('type' => 'TEXT'),
        'children' => array('type' => 'LOOKUP', 'remoteclass' => 'ModelTest_ModelChild', 'remotefield' => 'daddy'),
    );
}

class ModelTest_ModelChild extends Model
{
    protected $db = array(
        'id' => array('type' => 'ID'),
        'name' => array('type' => 'TEXT'),
        'daddy' => array('type' => 'FOREIGN', 'remoteclass' => 'ModelTest_Model', 'oninvalid' => 'RESTRICT'),
    );
}