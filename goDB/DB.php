<?php
/**
 * goDB2: библиотека для работы с реляционными базами данных из PHP
 *
 * @package go\DB
 * @link    https://github.com/vasa-c/go-db source
 * @link    https://github.com/vasa-c/go-db/wiki documentation
 * @version 2.0.1 beta
 * @author  Григорьев Олег aka vasa_c (http://blgo.ru/)
 * @license MIT (http://www.opensource.org/licenses/mit-license.php)
 * @uses    PHP >= 5.3
 * @uses    для каждого адаптера свои расширения
 */

namespace go\DB;

const VERSION = '2.0.1 beta';

abstract class DB
{

/*** STATIC: ***/

    /**
     * Создать объект для доступа к базе
     *
     * @throws \go\DB\Exceptions\Config
     *         неверные конфигурационные параметры
     * @throws \go\DB\Exceptions\Connect
     *         ошибка подключения
     *
     * @param array $params
     *        параметры подключения к базе
     * @param string $adapter [optional]
     *        адаптер базы (если не указан в $params)
     * @return \go\DB\DB
     *         объект для доступа к базе
     */
    final public static function create(array $params, $adapter = null) {
        $adapter = isset($params['_adapter']) ? $params['_adapter'] : $adapter;
        $adapter = \strtolower($adapter);
        $classname = __NAMESPACE__.'\\Adapters\\'.$adapter;
        if (!\class_exists($classname, true)) {
            throw new Exceptions\UnknownAdapter($adapter);
        }
        $params['_adapter'] = $adapter;
        return (new $classname($params));
    }

    /**
     * Получить список доступных адаптеров
     *
     * @return array
     */
    final public static function getAvailableAdapters() {
        if (!self::$availableAdapters) {
            $A = array();
            foreach (\glob(__DIR__.'/Adapters/*.php') as $filename) {
                if (\preg_match('~([a-z0-9]*)\.php$~s', $filename, $matches)) {
                    $A[] = $matches[1];
                }
            }
            self::$availableAdapters = $A;
        }
        return self::$availableAdapters;
    }

/*** PUBLIC: ***/

    /**
     * Выполнить запрос к базе данных
     *
     * @throws \go\DB\Exceptions\Connect
     *         ошибка при отложенном подключении
     * @throws \go\DB\Exceptions\Closed
     *         подключение закрыто
     * @throws \go\DB\Exceptions\Templater
     *         ошибка шаблонизатора запроса
     * @throws \go\DB\Exceptions\Query
     *         ошибка в запросе
     * @throws \go\DB\Exceptions\Fetch
     *         ошибка при разборе результата
     *
     * @param string $pattern
     *        шаблон запроса
     * @param array $data [optional]
     *        входящие данные для запроса
     * @param string $fetch [optional]
     *        формат представления результата
     * @param string $prefix [optional]
     *        префикс таблиц для данного конкретного запроса
     * @return \go\DB\Result
     *         результат в заданном формате
     */
    final public function query($pattern, $data = null, $fetch = null, $prefix = null) {
        $query = $this->makeQuery($pattern, $data, $prefix);
        return $this->plainQuery($query, $fetch);
    }

    /**
     * Выполнение "чистого" запроса
     *
     * @throws \go\DB\Exceptions\Connect
     * @throws \go\DB\Exceptions\Closed
     * @throws \go\DB\Exceptions\Query
     * @throws \go\DB\Exceptions\Fetch
     *
     * @param string $query
     *        SQL-запрос
     * @param string $fetch [optional]
     *        формат представления результата
     * @return \go\DB\Result
     *         результат в заданном формате
     */
    final public function plainQuery($query, $fetch = null) {
        $this->forcedConnect();
        $implementation = $this->connector->getImplementation();
        $connection     = $this->connector->getConnection();
        $duration       = \microtime(true);
        $cursor         = $implementation->query($connection, $query);
        $duration       = \microtime(true) - $duration;
        if (!$cursor) {
            $errorInfo  = $implementation->getErrorInfo($connection);
            $errorCode  = $implementation->getErrorCode($connection);
            throw new Exceptions\Query($query, $errorInfo, $errorCode);
        }
        $this->debugLog($query, $duration, null);
        $fetcher = $this->createFetcher($cursor);
        if (is_null($fetch)) {
            return $fetcher;
        }
        return $fetcher->fetch($fetch);
    }

    /**
     * Вызов объекта, как функции - переадресация на query()
     *
     * Следующие два примера идентичны:
     * @example $db->query('SELECT * FROM `table`');
     * @example $db('SELECT * FROM `table`');
     *
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
     * @return \go\DB\Result
     */
    final public function __invoke($pattern, $data = null, $fetch = null, $prefix = null) {
        return $this->query($pattern, $data, $fetch, $prefix);
    }

    /**
     * Установлено ли соединение фактически
     *
     * @return bool
     */
    final public function isConnected() {
        if ($this->hardClosed) {
            return false;
        }
        return $this->connector->isConnected();
    }

    /**
     * Закрыто ли соединение
     *
     * @return bool
     */
    final public function isClosed() {
        return $this->hardClosed;
    }


    /**
     * Принудительно установить соединение, если оно ещё не установлено
     *
     * @throws \go\DB\Exceptions\Connect
     *         ошибка подключения
     * @throws \go\DB\Exceptions\Closed
     *         подключение закрыто "жестким" образом
     */
    final public function forcedConnect() {
        if ($this->hardClosed) {
            throw new Exceptions\Closed();
        }
        if ($this->connected) {
            return false;
        }
        $res = $this->connector->connect();
        $this->connected = true;
        return $res;
    }

    /**
     * Закрыть соединение
     *
     * @param bool $soft [optional]
     *        "мягкое" закрытие: с возможностью восстановления
     */
    final public function close($soft = false) { // @todo close
        if ($this->hardClosed) {
            return false;
        }
        $result = false;
        if ($this->connected) {
            $result = $this->connector->close();
            $this->connected = false;
        }
        $this->hardClosed = !$soft;
        return $result;
    }

    /**
     * Установить префикс таблиц
     *
     * @param string $prefix
     */
    final public function setPrefix($prefix) {
        $this->prefix = $prefix;
        return true;
    }

    /**
     * Получить префикс таблиц
     *
     * @return string
     */
    final public function getPrefix() {
        return $this->prefix;
    }

    /**
     * Установить обработчик отладочной информации
     *
     * @param callback $callback
     *        обработчик (true - стандартный)
     */
    final public function setDebug($callback = true) { // @todo cli
        if ($callback === true) {
            if (php_sapi_name() == 'cli') {
                $callback = new Helpers\Debuggers\OutConsole();
            } else {
                $callback = new Helpers\Debuggers\OutHtml();
            }
        }
        $this->debugCallback = $callback;
        return true;
    }

    /**
     * Получить обработчик отладочной информации
     *
     * @return callback
     */
    final public function getDebug() {
        return $this->debugCallback;
    }

    /**
     * Отключить отправку отладочной информации
     */
    final public function disableDebug() {
        $this->debugCallback = null;
        return true;
    }

    /**
     * Получить внутреннюю реализацию подключения к базе
     *
     * @throws \go\DB\Exceptions\Connect
     * @throws \go\DB\Exceptions\Closed
     *
     * @param bool $connect
     *        подключиться, если не подключены
     * @return mixed
     *         низкоуровневая реализация или FALSE если ещё не создана
     */
    final public function getImplementationConnection($connect = true) {
        if ($connect && (!$this->connector->isConnected())) {
            $this->forcedConnect();
        }
        return $this->connector->getConnection();
    }

    /**
     * Сформировать запрос на основании шаблона и данных
     *
     * @throws \go\DB\Exceptions\Templater
     *
     * @param string $pattern
     * @param array $data
     * @param string $prefix
     * @return string
     */
    public function makeQuery($pattern, $data, $prefix = null) {
        $this->forcedConnect();
        if (is_null($prefix)) {
            $prefix = $this->prefix;
        }
        $templater = $this->createTemplater($pattern, $data, $prefix);
        $templater->parse();
        return $templater->getQuery();
    }

/*** PROTECTED: ***/

    /**
     * Скрытый конструктор - извне не создать
     *
     * @param array $params
     *        конфигурационные параметры базы
     */
    protected function __construct($params) {
        $this->separateParams($params);
        $this->connector = $this->createConnector();
        if (!$this->paramsSys['lazy']) {
            $this->connector->connect();
            $this->connected = true;
        }
        $this->setPrefix($this->paramsSys['prefix']);
        $this->setDebug($this->paramsSys['debug']);
    }

    /**
     * Деструктор
     */
    public final function __destruct() {
        $this->connector->close();
        $this->connector->removeLink();
        $this->connector = null;
    }

    /**
     * Обработчик клонирования объекта
     */
    public function __clone() {
        $this->connector->addLink($this->connected);
    }

    /**
     * Создать объект подключения к базе
     *
     * @return \go\DB\Helpers\Connector
     */
    protected function createConnector() {
        return (new Helpers\Connector($this->paramsSys['adapter'], $this->paramsDB));
    }

    /**
     * Создать шаблонизатор запроса
     *
     * @param string $pattern
     * @param array $data
     * @param string $prefix
     * @return \go\DB\Helpers\Templater
     */
    protected function createTemplater($pattern, $data, $prefix) {
        return (new Helpers\Templater($this->connector, $pattern, $data, $prefix));
    }

    /**
     * Создать объект представления результата
     *
     * @param mixed $cursor
     * @return \go\DB\Result
     */
    protected function createFetcher($cursor) {
        return (new Helpers\Fetcher($this->connector, $cursor));
    }

    /**
     * Разбор и сортировка параметров на системные и подключения
     *
     * @throws \go\DB\Exceptions\ConfigSys
     *
     * @param array $params
     */
    protected function separateParams($params) {
        $this->paramsDB  = array();
        $this->paramsSys = \go\DB\Helpers\Config::get('configsys');
        foreach ($params as $name => $value) {
            if ((!empty($name)) && ($name[0] == '_')) {
                $name = substr($name, 1);
                if (!\array_key_exists($name, $this->paramsSys)) {
                    throw new Exceptions\ConfigSys('Unknown system param "'.$name.'"');
                }
                $this->paramsSys[$name] = $value;
            } else {
                $this->paramsDB[$name] = $value;
            }
        }
        return true;
    }

    /**
     * Отправка запроса в отладчик
     *
     * @param string $query
     * @param float $duration
     * @param mixed $info
     */
    protected function debugLog($query, $duration, $info) {
        if ($this->debugCallback) {
            \call_user_func($this->debugCallback, $query, $duration, $info);
        }
        return true;
    }

/*** VARS: ***/

    /**
     * Кэш списка доступных адаптеров
     *
     * @var array
     */
    private static $availableAdapters;

    /**
     * Объект-подключалка
     *
     * @var \go\DB\Helpers\Connector
     */
    protected $connector;

    /**
     * Системные параметры
     *
     * @var array
     */
    protected $paramsSys;

    /**
     * Параметры подключения к базе
     *
     * @var array
     */
    protected $paramsDB;

    /**
     * Текущий префикс имён таблиц
     *
     * @var string
     */
    protected $prefix;

    /**
     * Отладчик запросов
     *
     * @var callback
     */
    protected $debugCallback;

    /**
     * Установлено ли подключение для данного объекта базы
     *
     * @var bool
     */
    protected $connected = false;

    /**
     * Закрыто ли подключение жёстким образом
     *
     * @var bool
     */
    protected $hardClosed = false;
}

/**
 * Создать объект для доступа к базе
 * (алиас DB::create)
 *
 * @throws \go\DB\Exceptions\Config
 * @throws \go\DB\Exceptions\Connect
 *
 * @param array $params
 *        параметры подключения к базе
 * @param string $adapter [optional]
 *        адаптер базы (если не указан в $params)
 * @return \go\DB\DB
 *         объект для доступа к базе
 */
function create(array $params, $adapter = null) {
    return DB::create($params, $adapter);
}

/**
 * Запрос к центральной базе центрального хранилища
 * (алиас Storage::query)
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
function query($pattern, $data = null, $fetch = null, $prefix = null) {
    return Storage::getInstance()->query($pattern, $data, $fetch, $prefix);
}