<?php
/**
 * Хелпер создания тестовых таблиц
 *
 * @package    go\DB
 * @subpackage Tests
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\Tests\DB\Adapters\_helpers;

abstract class Base
{
    /**
     * Структуры тестовых таблиц
     *
     * @var array
     */
    protected $testTables = array(
        'test_table' => array(
            'cols' => array(
                'id'     => 'ID',
                'name'   => 'STRING',
                'number' => 'INT',
            ),
            'keys' => array(
                'primary' => 'id',
            ),
            'data' => array(
                array(1, 'one',   2),
                array(2, 'two',   null),
                array(3, 'three', 6),
                array(4, 'four',  null),
                array(5, 'five',  10),
            ),
        ),
        'test_vars' => array(
            'cols' => array(
                'key'   => 'STRING',
                'value' => 'STRING',
            ),
            'keys' => array(
                'primary' => array('key', 'value'),
            ),
            'data' => array(
                array('name', 'goDB'),
                array('pi',   '3,14'),
                array('e',    '2,7'),
                array('year', '2010'),
                array('three', 33),
            ),
        ),
    );

    /**
     * Описание типов столбцов
     * (переопределяется у потомков)
     * 
     * @var array
     */
    protected $testTypes = array(
        'ID'     => null,
        'STRING' => null,
        'INT'    => null,
    );
    
    /**
     * Описание ключей
     * (переопределяется у потомков)
     * 
     * @var array
     */
    protected $testKeys = array(
        'primary' => NULL,
    );

    protected $PATTERN_DROP_QUERY = 'DROP TABLE IF EXISTS ?table:table';
    protected $PATTERN_CREATE_QUERY = 'CREATE TABLE ?table:table (?query:cols;?query:keys)';

    /**
     * Получить объект хелпера для указанного адаптера
     *
     * @param string $adapter
     * @return \go\Tests\DB\Adapters\_helpers\Base
     */
    public static function getHelperForAdapter($adapter) {
        if (!isset(self::$cacheHelpers[$adapter])) {
            $classname = __NAMESPACE__.'\\'.$adapter;
            if (!\class_exists($classname, false)) {
                require_once(__DIR__.'/'.$adapter.'.php');
            }
            self::$cacheHelpers[$adapter] = new $classname($adapter);
        }
        return self::$cacheHelpers[$adapter];
    }

    /**
     * Закрытый конструктор - извне не создать
     *
     * @param string $adapter
     */
    protected function __construct($adapter) {
        $this->adapter = $adapter;
    }

    /**
     * Получить конфигурацию подключения
     *
     * @return array
     */
    public function getConfig() {
        return \go\Tests\DB\Config::getInstance()->__get($this->adapter);
    }

    /**
     * Создать объект goDB
     *
     * @param mixed $fill
     *        FALSE  - ничего не создавать
     *        TRUE   - создать таблицы
     *        "fill" - наполнить таблицы тестовыми данными
     * @return \go\DB\DB
     */
    public function createDB($fill = false) {
        $this->db = \go\DB\DB::create($this->getConfig(), $this->adapter);
        // $this->db->setDebug((function($query) {echo $query.";\n";}));
        $this->toFill($fill);
        return $this->db;
    }

    /**
     * Получить объект goDB (создать если нет)
     *
     * @param mixed $fill
     *        FALSE  - ничего не создавать
     *        TRUE   - создать таблицы
     *        "fill" - наполнить таблицы тестовыми данными
     * @return \go\DB\DB
     */
    public function getDB($fill = false) {
        if ($this->db) {
            $this->toFill($fill);
            return $this->db;
        }
        return $this->createDB($fill);
    }

    /**
     * Указать, что в рамках теста, тестовая таблица была уничтожена
     */
    public function dropped() {
        $this->created = false;
        $this->filled  = false;
        return true;
    }

    /**
     * Указать, что рамках теста, тестовая таблица была изменена
     */
    public function updated() {
        $this->filled = false;
        return true;
    }

    /**
     * Указать, что в рамках теста, тестовая БД была закрыта жёстким образом
     */
    public function closed() {
        $this->db = null;
        return true;
    }

    /**
     * Установить объект тестовой БД
     * 
     * @param DB $db
     */
    public function setDB(\go\DB\DB $db) {
        $this->db = $db;
        return true;
    }

    /**
     * Приведение состояния базы к указанному
     *
     * @param mixed $fill
     *        FALSE  - ничего не создавать
     *        TRUE   - создать таблицы
     *        "fill" - наполнить таблицы тестовыми данными
     */
    protected function toFill($fill) {
        if ($fill) {
            $this->createTestTables();
            if ($fill === 'fill') {
                $this->fillTestTables();
            }
        }
        return true;
    }

    /**
     * Создать набор тестовых таблиц
     */
    protected function createTestTables() {
        if ($this->created) {
            return true;
        }
        foreach ($this->testTables as $table => $struct) {
            $this->createSingleTestTable($table, $struct);
        }
        $this->created = true;
        $this->filled  = false;
        return true;
    }

    /**
     * Заполнить тестовые таблицы тестовыми данными
     */
    protected function fillTestTables() {
        if ($this->filled) {
            return true;
        }
        foreach ($this->testTables as $table => $struct) {
            $this->fillSingleTestTable($table, $struct['data']);
        }
        $this->filled = true;
        return true;
    }

    /**
     * Создать одну тестовую таблицу
     *
     * @param string $table
     *        имя таблицы
     * @param array $struct
     *        структура таблицы
     */
    protected function createSingleTestTable($table, array $struct) {
        $this->dropSingleTestTable($table);
        $data = array(
            'table' => $table,
            'cols'  => $this->getQueryForCols($struct['cols']),
            'keys'  => $this->getQueryForKeys($struct['keys']),
        );
        $this->db->query($this->PATTERN_CREATE_QUERY, $data);
        return true;
    }

    /**
     * Удалить одну тестовую таблицу
     *
     * @param string $table
     */
    protected function dropSingleTestTable($table) {
        $this->db->query($this->PATTERN_DROP_QUERY, array('table' => $table));
        return true;
    }

    /**
     * Заполнить одну тестовую таблицу тестовыми данными
     * 
     * @param string $table
     *        имя таблицы
     * @param array $data
     *        тестовые данные
     */
    protected function fillSingleTestTable($table, array $data) {
        $this->truncateSingleTestTable($table);
        $pattern = 'INSERT INTO ?table VALUES ?values-null';
        $this->db->query($pattern, array($table, $data));
        return true;
    }

    protected function truncateSingleTestTable($table) {
        $this->db->query('TRUNCATE TABLE ?table', array($table));
        return true;
    }

    /**
     * Часть запроса для CREATE TABLE со столбцами
     *
     * @param array $cols
     * @return string
     */
    protected function getQueryForCols(array $cols) {
        $pattern = array();
        $data    = array();
        foreach ($cols as $col => $type) {
            $pattern[] = '?col ?escape';
            $data[] = $col;
            $data[] = $this->testTypes[$type];
        }
        $pattern = implode(',', $pattern);
        return $this->db->makeQuery($pattern, $data);
    }

    /**
     * Часть запроса для CREATE TABLE со ключами
     *
     * @param array $keys
     * @return string
     */
    protected function getQueryForKeys(array $keys) {
        if (empty($keys)) {
            return '';
        }
        $result = array();
        foreach ($keys as $k => $cols) {
            $result[] = $this->db->makeQuery($this->testKeys[$k], array($cols));
        }
        return ','.implode(',', $result);
    }

    /**
     * Кэш хелперов для адаптеров
     * 
     * @var array
     */
    private static $cacheHelpers = array();

    /**
     * Текущий адаптер
     * 
     * @var string
     */
    protected $adapter;

    /**
     * Тестовая БД
     *
     * @var \go\DB\DB
     */
    protected $db;

    /**
     * Созданы ли уже таблицы
     *
     * @var bool
     */
    protected $created = false;

    /**
     * Заполнены ли таблицы тестовыми данными
     * 
     * @var bool
     */
    protected $filled = false;
}