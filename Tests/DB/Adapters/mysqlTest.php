<?php
/**
 * Тестирование адаптера mysql (надстройка над php_mysqli)
 *
 * @package    go\DB
 * @subpackage Tests
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\Tests\DB\Adapters;

require_once(__DIR__.'/Base.php');

/**
 * @covers \go\DB\Adapters\mysql
 */
class mysqlTest extends Base
{
    public function testUseDB() {
        $helper = $this->getHelper();
        $helper->createDB(true);
        $config = $this->getHelper()->getConfig();

        $db1    = \go\DB\DB::create($config, $this->getAdapter());
        $tables = $db1->query('SHOW TABLES')->col();
        $this->assertContains('test_table', $tables);
        unset($db1);

        $dbname = $config['dbname'];
        unset($config['dbname']);
        $db2    = \go\DB\DB::create($config, $this->getAdapter());
        $databases = $db2->query('SHOW DATABASES')->col();
        $this->assertContains($dbname, $databases);

        $this->setExpectedException('go\DB\Exceptions\Query', null, 1046); // 1046 - No database selected
        $tables = $db2->query('SHOW TABLES')->col();
    }

    public function testCharset() {
        $config = $this->getHelper()->getConfig();

        $config['charset'] = 'utf8';
        $db = \go\DB\DB::create($config, $this->getAdapter());
        $this->assertEquals('utf8', $db->query('SELECT @@character_set_client')->el());

        $config['charset'] = 'latin1';
        $db = \go\DB\DB::create($config, $this->getAdapter());
        $this->assertEquals('latin1', $db->query('SELECT @@character_set_client')->el());

        $this->setExpectedException('go\DB\Exceptions\Connect'); // charset error (1115 или 2019)
        $config['charset'] = 'efineifnerunt';
        $db = \go\DB\DB::create($config, $this->getAdapter());
        $db->forcedConnect();
    }
}