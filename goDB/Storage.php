<?php
/**
 * Хранилище объектов баз данных
 *
 * @package    go\DB
 * @subpackage Storage
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\DB;

final class Storage
{

/*** STATIC: ***/

    /**
     * Получить центральный объект хранилища
     *
     * @return \go\DB\Storage
     */
    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Установить центральный объект хранилища
     * 
     * @param mixed $instance
     *        экземпляр хранилища или параметры баз для заполнения
     * @return \go\DB\Storage
     */
    public static function setInstance($instance) {
        if ($instance instanceof self) {
            self::$instance = $instance;
        } elseif (is_array($instance)) {
            self::$instance = new self($instance);
        } else {
            $message = 'Argument 1 passed to Storage::setInstance must be '.
                'an instance of go\DB\DB or array, '.gettype($instance).' given';
            trigger_error($message, \E_USER_ERROR);
        }
        return self::$instance;
    }

    /**
     * Запрос к центральной базе центрального хранилища
     * 
     * @throws \go\DB\Exceptions\StorageDBCentral
     *         нет центральной базы
     * @throws \go\DB\Exceptions\Connect
     * @throws \go\DB\Exceptions\Closed
     * @throws \go\DB\Exceptions\Templater
     * @throws \go\DB\Exceptions\Query
     * @throws \go\DB\Exceptions\Fetch
     *
     * @param string $pattern
     * @param array $data [optional]
     * @param string $fetch [optional]
     * @param string $prefix [optional]
     */
    public static function query($pattern, $data = null, $fetch = null, $prefix = null) {
        return self::getInstance()->__invoke($pattern, $data, $fetch, $prefix);
    }
    
/*** PUBLIC: ***/

    /**
     * Конструктор хранилища
     *
     * @throws \go\DB\Exceptions\StorageAssoc
     *         ошибка ассоциации при заполнении
     * 
     * @param array $mparams [optional]
     *        параметры для заполнения (не указаны - пустое хранилище)
     */
    public function __construct($mparams = null) {
        if ($mparams) {
            $this->fill($mparams);
        }
        return true;
    }

    /**
     * Получить объект базы по имени
     *
     * @throws \go\DB\Exceptions\StorageNotFound
     *         нет такой базы
     *
     * @param string $name [optional]
     * @return \go\DB\DB
     */
    public function get($name = '') {
        if (!isset($this->dbs[$name])) {
            throw new Exceptions\StorageNotFound($name);
        }
        return $this->dbs[$name];
    }

    /**
     * Создать объект базы и сохранить в хранилище
     *
     * @throws \go\DB\Exceptions\StorageEngaged
     *         данное имя уже занято
     * @throws \go\DB\Exceptions\Config
     * @throws \go\DB\Exceptions\Connect
     *
     * @param array $params
     *        параметры подключения
     * @param string $name [optional]
     *        имя в хранилище
     * @return \go\DB\DB
     *         объект созданной базы
     */
    public function create(array $params, $name = '') {
        $db = \go\DB\DB::create($params);
        $this->set($db, $name);
        return $db;
    }

    /**
     * Записать базу в хранилище
     *
     * @throws \go\DB\Exceptions\StorageEngaged
     *         данное имя уже занято
     *
     * @param \go\DB\DB $db
     *        объект базы
     * @param string $name
     *        имя в хранилище
     */
    public function set(DB $db, $name = '') {
        if (isset($this->dbs[$name])) {
            throw new Exceptions\StorageEngaged($name);
        }
        $this->dbs[$name] = $db;
        return true;
    }

    /**
     * Заполнить хранилище создаваемыми базами
     *
     * @throws \go\DB\Exceptions\StorageAssoc
     *         ошибка ассоциации при заполнении
     * @throws \go\DB\Exceptions\StorageEngaged
     *         одно из имён уже занято
     *
     * @param array $mparams
     *        параметры баз данных
     */
    public function fill(array $mparams) {
        $assocs = array();
        foreach ($mparams as $name => $params) {
            if (is_array($params)) {
                $this->create($params, $name);
            } elseif (isset($mparams[$params])) {
                $assocs[$name] = $params;
            } else {
                throw new Exceptions\StorageAssoc($params);
            }
        }
        foreach ($assocs as $name => $assoc) {
            $this->set($this->get($assoc), $name);
        }
        return true;
    }

    /**
     * Существует ли уже в хранилище база с указанным именем
     *
     * @param string $name
     * @return bool
     */
    public function exists($name = '') {
        return isset($this->dbs[$name]);
    }

    /**
     * Вызов хранилища, как функции - запрос к центральной базе хранилища
     * 
     * @throws \go\DB\Exceptions\StorageDBCentral
     * @throws \go\DB\Exceptions\Connect
     * @throws \go\DB\Exceptions\Closed
     * @throws \go\DB\Exceptions\Templater
     * @throws \go\DB\Exceptions\Query
     * @throws \go\DB\Exceptions\Fetch
     *
     * @param string $pattern [optional]
     * @param array $data [optional]
     * @param string $fetch [optional]
     * @param string $prefix [optional]
     * @return mixed
     */
    public function __invoke($pattern, $data = null, $fetch = null, $prefix = null) {
        if (!isset($this->dbs[''])) {
            throw new Exceptions\StorageDBCentral('');
        }
        return $this->dbs['']->query($pattern, $data, $fetch, $prefix);
    }

    /**
     * @override magic method
     *
     * @example $db = $storage->dbname
     *
     * @throws \go\DB\Exceptions\StorageNotFound
     *
     * @param string $name
     * @return \go\DB\DB
     */
    public function __get($name) {
        return $this->get($name);
    }

    /**
     * @override magic method
     *
     * @example $storage->dbname = $db
     *
     * @throws \go\DB\Exceptions\StorageEngaged
     *
     * @param string $name
     * @param \go\DB\DB $value
     */
    public function __set($name, $value) {
        return $this->set($value, $name);
    }

    /**
     * @override magic method
     *
     * @example isset($storage->dbname)
     *
     * @param string $name
     * @return bool
     */
    public function __isset($name) {
        return $this->exists($name);
    }
    
/*** PRIVATE: ***/
    
/*** VARS: ***/

    /**
     * Центральное хранилище
     * 
     * @var \go\DB\Storage
     */
    private static $instance;

    /**
     * Хранимые базы
     * 
     * @var array
     */
    private $dbs = array();
}