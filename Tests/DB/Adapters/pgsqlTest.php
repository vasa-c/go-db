<?php
/**
 * Тестирование адаптера pgsql (надстройка над php_pgsql)
 *
 * @package    go\DB
 * @subpackage Tests
 * @author     Alex Polev
 */

namespace go\Tests\DB\Adapters;

require_once(__DIR__.'/Base.php');

/**
 * @covers \go\DB\Adapters\mysql
 */
class pgsqlTest extends Base
{
	protected $PATTERN_SHOW_TABLES  =  'SELECT table_name
										FROM information_schema.tables
										WHERE table_schema = \'public\'';

	public function testUseDB() {
        $helper = $this->getHelper();
        $helper->createDB(true);

        $config = $this->getHelper()->getConfig();

        $db1    = \go\DB\DB::create($config, $this->getAdapter());
        $tables = $db1->query($this->PATTERN_SHOW_TABLES)->col();
        $this->assertContains('test_table', $tables);
        unset($db1);

        $dbname = $config['dbname'];
        unset($config['dbname']);
        $db2    = \go\DB\DB::create($config, $this->getAdapter());
        $databases = $db2->query('select datname from pg_database')->col();
        $this->assertContains($dbname, $databases);
		unset($db2);

		$this->getHelper()->closed();
    }


	public function testCharset() {
        $config = $this->getHelper()->getConfig();

        $config['charset'] = 'UTF8';
        $db = \go\DB\DB::create($config, $this->getAdapter());
        $this->assertEquals('UTF8', $db->query('SHOW client_encoding')->el());

        $config['charset'] = 'latin1';
        $db = \go\DB\DB::create($config, $this->getAdapter());
        $this->assertEquals('latin1', $db->query('SHOW client_encoding')->el());

        $this->setExpectedException('go\DB\Exceptions\Connect'); // charset error 
        $config['charset'] = 'efineifnerunt';
        $db = \go\DB\DB::create($config, $this->getAdapter());
        $db->forcedConnect();
    }
}