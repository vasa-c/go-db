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
    /**
     * Получить центральный объект хранилища
     *
     * @return \go\DB\Storage
     */
    public static function getInstance()
    {
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
     * @todo почему trigger_error вместо исключения?
     */
    public static function setInstance($instance)
    {
        if ($instance instanceof self) {
            self::$instance = $instance;
        } elseif (\is_array($instance)) {
            self::$instance = new self($instance);
        } else {
            $message = 'Argument 1 passed to Storage::setInstance must be '.
                'an instance of go\DB\DB or array, '.\gettype($instance).' given';
            \trigger_error($message, \E_USER_ERROR);
        }
        return self::$instance;
    }

    /**
     * Запрос к центральной базе центрального хранилища
     *
     * @param string $pattern
     * @param array $data [optional]
     * @param string $fetch [optional]
     * @param string $prefix [optional]
     * @throws \go\DB\Exceptions\StorageDBCentral
     *         нет центральной базы
     * @throws \go\DB\Exceptions\Connect
     * @throws \go\DB\Exceptions\Closed
     * @throws \go\DB\Exceptions\Templater
     * @throws \go\DB\Exceptions\Query
     * @throws \go\DB\Exceptions\Fetch
     */
    public static function query($pattern, $data = null, $fetch = null, $prefix = null)
    {
        return self::getInstance()->__invoke($pattern, $data, $fetch, $prefix);
    }

    /**
     * Конструктор хранилища
     *
     * @param array $mparams [optional]
     *        параметры для заполнения (не указаны - пустое хранилище)
     * @throws \go\DB\Exceptions\StorageAssoc
     *         ошибка ассоциации при заполнении
     */
    public function __construct($mparams = null)
    {
        if ($mparams) {
            $this->fill($mparams);
        }
        return true;
    }

    /**
     * Получить объект базы по имени
     *
     * @param string $name [optional]
     * @return \go\DB\DB
     * @throws \go\DB\Exceptions\StorageNotFound
     *         нет такой базы
     */
    public function get($name = '')
    {
        if (!isset($this->dbs[$name])) {
            throw new Exceptions\StorageNotFound($name);
        }
        return $this->dbs[$name];
    }

    /**
     * Создать объект базы и сохранить в хранилище
     *
     * @param array $params
     *        параметры подключения
     * @param string $name [optional]
     *        имя в хранилище
     * @return \go\DB\DB
     *         объект созданной базы
     * @throws \go\DB\Exceptions\StorageEngaged
     *         данное имя уже занято
     * @throws \go\DB\Exceptions\Config
     * @throws \go\DB\Exceptions\Connect
     */
    public function create(array $params, $name = '')
    {
        $db = \go\DB\DB::create($params);
        $this->set($db, $name);
        return $db;
    }

    /**
     * Записать базу в хранилище
     *
     * @param \go\DB\DB $db
     *        объект базы
     * @param string $name
     *        имя в хранилище
     * @throws \go\DB\Exceptions\StorageEngaged
     *         данное имя уже занято
     */
    public function set(DB $db, $name = '')
    {
        if (isset($this->dbs[$name])) {
            throw new Exceptions\StorageEngaged($name);
        }
        $this->dbs[$name] = $db;
        return true;
    }

    /**
     * Заполнить хранилище создаваемыми базами
     *
     * @param array $mparams
     *        параметры баз данных
     * @throws \go\DB\Exceptions\StorageAssoc
     *         ошибка ассоциации при заполнении
     * @throws \go\DB\Exceptions\StorageEngaged
     *         одно из имён уже занято
     */
    public function fill(array $mparams)
    {
        $assocs = array();
        foreach ($mparams as $name => $params) {
            if (\is_array($params)) {
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
    public function exists($name = '')
    {
        return isset($this->dbs[$name]);
    }

    /**
     * Вызов хранилища как функции - запрос к центральной базе хранилища
     *
     * @param string $pattern [optional]
     * @param array $data [optional]
     * @param string $fetch [optional]
     * @param string $prefix [optional]
     * @return mixed
     * @throws \go\DB\Exceptions\StorageDBCentral
     * @throws \go\DB\Exceptions\Connect
     * @throws \go\DB\Exceptions\Closed
     * @throws \go\DB\Exceptions\Templater
     * @throws \go\DB\Exceptions\Query
     * @throws \go\DB\Exceptions\Fetch
     */
    public function __invoke($pattern, $data = null, $fetch = null, $prefix = null)
    {
        if (!isset($this->dbs[''])) {
            throw new Exceptions\StorageDBCentral('');
        }
        return $this->dbs['']->query($pattern, $data, $fetch, $prefix);
    }

    /**
     * Magic get
     *
     * @example $db = $storage->dbname
     *
     * @param string $name
     * @return \go\DB\DB
     * @throws \go\DB\Exceptions\StorageNotFound
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * Magic set
     *
     * @example $storage->dbname = $db
     *
     * @param string $name
     * @param \go\DB\DB $value
     * @throws \go\DB\Exceptions\StorageEngaged
     */
    public function __set($name, $value)
    {
        return $this->set($value, $name);
    }

    /**
     * Magic isset
     *
     * @example isset($storage->dbname)
     *
     * @param string $name
     * @return bool
     */
    public function __isset($name)
    {
        return $this->exists($name);
    }

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
