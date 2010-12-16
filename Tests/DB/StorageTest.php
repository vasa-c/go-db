<?php
/**
 * Тестирование хранилищ баз данных
 *
 * @package    go\DB
 * @subpackage Tests
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\Tests\DB;

require_once(__DIR__.'/../Tests.php');

use go\DB\Storage as Storage;

/**
 * @covers \go\DB\Storage
 */
final class StorageTest extends \go\Tests\DB\Base
{

    /**
     * @covers getInstance
     * @covers setInstance
     */
    public function testInstance() {
        $instance1 = Storage::getInstance();
        $instance2 = Storage::getInstance();
        $this->assertSame($instance1, $instance2);

        $instance3 = new Storage();
        $instance4 = Storage::setInstance($instance3);
        $instance5 = Storage::getInstance();
        $this->assertSame($instance3, $instance4);
        $this->assertSame($instance3, $instance5);
        $this->assertNotSame($instance3, $instance1);

        Storage::setInstance($instance1);
    }

    /**
     * @covers get
     * @covers exists
     * @covers __get
     * @covers __set
     * @covers __isset
     */
    public function testGet() {
        $db = \go\DB\DB::create(array('host' => 'localhost'), 'test');
        $storage = new Storage();

        $this->assertFalse($storage->exists('dbname'));
        $this->assertFalse(isset($storage->dbname));

        $storage->set($db, 'dbname');
        $this->assertTrue($storage->exists('dbname'));
        $this->assertTrue(isset($storage->dbname));
        $this->assertSame($db, $storage->get('dbname'));
        $this->assertSame($db, $storage->dbname);

        $this->assertFalse($storage->exists('othername'));
        $this->assertFalse(isset($storage->othername));

        $this->setExpectedException('go\DB\Exceptions\StorageNotFound');
        $storage->get('othername');
    }

    /**
     * @covers create
     */
    public function testCreate() {
        $params = array(
            '_adapter' => 'test',
            'host'     => 'localhost',
        );

        $storage = new Storage();

        $this->assertFalse($storage->exists('dbname'));
        $db = $storage->create($params, 'dbname');

        $this->assertInstanceOf('go\DB\DB', $db);
        $this->assertSame($db, $storage->get('dbname'));

        $this->setExpectedException('go\DB\Exceptions\StorageEngaged');
        $storage->create($params, 'dbname');
    }

    /**
     * @covers set
     */
    public function testSet() {
        $db = \go\DB\DB::create(array('host' => 'localhost'), 'test');
        $storage = new Storage();

        $storage->set($db, 'name1');
        $storage->name2 = $db;

        $this->assertSame($db, $storage->name1);
        $this->assertSame($db, $storage->name2);

        $this->setExpectedException('go\DB\Exceptions\StorageEngaged');
        $storage->set($db, 'name1');
    }

    /**
     * @covers fill
     * @covers __construct
     * @covers setInstance
     */
    public function testFill() {
        $dbs = array(
            'one' => array(
                '_adapter' => 'test',
                'host'     => 'localhost',
            ),
            'two' => array(
                '_adapter' => 'test',
                'host'     => 'localhost',
            ),
            'three' => 'one',
        );

        $storage1 = new Storage();
        $this->assertFalse($storage1->exists('one'));
        $storage1->fill($dbs);
        $this->assertSame($storage1->one, $storage1->three);
        $this->assertNotSame($storage1->one, $storage1->two);

        $storage2 = new Storage($dbs);
        $this->assertTrue($storage1->exists('one'));

        $instance = \go\DB\Storage::getInstance();
        $storage3 = \go\DB\Storage::setInstance($dbs);
        $this->assertTrue($storage3->exists('one'));
        \go\DB\Storage::setInstance($instance);

        $this->setExpectedException('go\DB\Exceptions\StorageEngaged');
        $storage2->fill($dbs);
    }

    /**
     * @covers fill
     * @expectedException go\DB\Exceptions\StorageAssoc
     */
    public function testExceptionStorageAssoc() {
        $dbs = array(
            'two' => array(
                '_adapter' => 'test',
                'host'     => 'localhost',
            ),
            'three' => 'one',
        );
        $storage = new Storage($dbs);
    }

    public function testCentralDB() {
        $db = \go\DB\DB::create(array('host' => 'localhost'), 'test');
        $storage = new Storage();

        $this->assertFalse($storage->exists());
        $storage->set($db);
        $this->assertTrue($storage->exists());
        $this->assertSame($db, $storage->get());

        $storage2 = new Storage();
        $this->setExpectedException('go\DB\Exceptions\StorageDBCentral');
        $storage2('INSERT');
    }

    /**
     * @covers __invoke
     */
    public function testInvoke() {
        $storage = new Storage();
        $storage->create(array('host' => 'localhost', '_adapter' => 'test'));

        $id1 = $storage('INSERT', null, 'id');
        $id2 = $storage('INSERT', null, 'id');
        $this->assertEquals($id1 + 1, $id2);
    }

    /**
     * @covers query
     * @covers go\DB\query
     */
    public function testQuery() {       
        $instance = Storage::getInstance();

        $db = \go\DB\DB::create(array('host' => 'localhost'), 'test');
        $storage = new Storage();

        $id1 = $db->query('INSERT')->id();
        $storage->set($db);
        $id2 = $storage('INSERT')->id();
        $this->assertEquals($id1 + 1, $id2);

        Storage::setInstance($storage);
        $id3 = Storage::query('INSERT')->id();
        $this->assertEquals($id2 + 1, $id3);

        $id4 = \go\DB\query('INSERT')->id();
        $this->assertEquals($id3 + 1, $id4);
        
        Storage::setInstance($instance);
    }
}