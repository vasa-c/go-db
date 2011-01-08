<?php
/**
 * Шаблонизатор запроса
 *
 * По шаблону и входящим данным формирует итоговый запрос
 *
 * @package    go\DB
 * @subpackage Helpers
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\DB\Helpers;

use go\DB\Exceptions as Exceptions;

class Templater
{
    /**
     * Конструктор
     *
     * @param \go\DB\Helpers\Connector $connector
     *        подключалка к базе (подключение должно быть установлено)
     * @param string $pattern
     *        шаблон запроса
     * @param array $data
     *        входные данные
     * @param string $prefix
     *        префикс запроса
     */
    public function __construct(Connector $connector, $pattern, $data, $prefix) {
        $this->implementation = $connector->getImplementation();
        $this->connection     = $connector->getConnection();
        $this->pattern        = $pattern;
        $this->data           = $data ?: array();
        $this->prefix         = $prefix;
    }

    /**
     * Шаблонизация запроса
     *
     * @throws go\DB\Exceptions\Templater
     *         ошибки при шаблонизации
     *
     * @return string
     *         итоговые запрос
     */
    public function parse() {
        if (!\is_null($this->query)) {
            return $this->query;
        }
        /* Замена {table} */
        $query    = \preg_replace_callback('~{(.*?)}~', array($this, '_table'), $this->pattern);
        /* Замена плейсхолдеров */
        $pattern  = '~\?([a-z\?-]+)?(:([a-z0-9_-]*))?;?~i';
        $callback = array($this, '_placeholder');
        $query    = \preg_replace_callback($pattern, $callback, $query);
        if ((!$this->named) && (\count($this->data) > $this->counter)) {
            throw new \go\DB\Exceptions\DataMuch(count($this->data), $this->counter);
        }
        $this->query = $query;
        return $this->query;
    }

    /**
     * Получить итоговый запрос
     *
     * @return string
     */
    public function getQuery() {
        return $this->query;
    }

    /**
     * Замена имени таблицы "{table}"
     */
    private function _table($matches) {
        return $this->implementation->reprTable($this->connection, $this->prefix.$matches[1]);
    }

    /**
     * Callback обработки запроса
     *
     * @throws \go\DB\Exceptions\Templaters
     *         ошибка при разборе
     *
     * @param array $matches
     *        параметры очередного плейсхолдера
     * @return string
     *         на что его заменить
     */
    private function _placeholder($matches) {
        $placeholder = isset($matches[1]) ? $matches[1] : '';
        if (isset($matches[3])) {
            $name = $matches[3];
            if (empty($name)) {
                /* Именованный плейсхолдер без имени ("?set:") */
                throw new \go\DB\Exceptions\UnknownPlaceholder($matches[0]);
            }
        } else {
            $name = null;
            if ($placeholder == '?') { // ?? - вставка вопросительного знака
                return '?';
            }
        }
        if ($name) {
            if ($this->counter == 0) {
                $this->named = true;
            } elseif (!$this->named) {
                /* Именованный плейсхолдер, хотя уже использовались регулярные */
                throw new \go\DB\Exceptions\MixedPlaceholder($matches[0]);
            }
            if (!array_key_exists($name, $this->data)) {
                throw new \go\DB\Exceptions\DataNamed($name);
            }
            $value = $this->data[$name];
        } elseif ($this->named) {
            /* Регулярный плейсхолдер, хотя уже использовались именованные */
            throw new \go\DB\Exceptions\MixedPlaceholder($matches[0]);
        } else {
            if (!array_key_exists($this->counter, $this->data)) {
                /* Данные для регулярных плейсхолдеров закончились */
                throw new \go\DB\Exceptions\DataNotEnough(count($this->data), $this->counter);
            }
            $value = $this->data[$this->counter];
        }
        $this->counter++;
        $parser   = new ParserPH($placeholder);
        $type     = $parser->getType();
        $modifers = $parser->getModifers();
        $method   = 'replacement_'.$type;
        return $this->$method($value, $modifers);
    }

    /**
     * Преобразование скалярного значения в соответствии с модификаторами плейсхолдера
     *
     * @param mixed $value
     * @param array $modifers
     * @return string
     */
    private function valueModification($value, array $modifers) {
        if ($modifers['n'] && is_null($value)) {
            return $this->implementation->reprNULL($this->connection);
        }
        if ($modifers['i']) {
            return $this->implementation->reprInt($this->connection, $value);
        } elseif ($modifers['f']) {
            return $this->implementation->reprFloat($this->connection, $value);
        } elseif ($modifers['b']) {
            return $this->implementation->reprBool($this->connection, $value);
        }
        return $this->implementation->reprString($this->connection, $value);
    }

    /**
     * ?, ?string, ?scalar
     *
     * @param mixed $value
     * @param array $modifers
     * @return string
     */
    private function replacement_($value, array $modifers) {
        return $this->valueModification($value, $modifers);
    }

    /**
     * ?l, ?list
     *
     * @param array $value
     * @param array $modifers
     * @return string
     */
    private function replacement_l(array $value, array $modifers) {
        $values = array();
        foreach ($value as $element) {
            $values[] = $this->valueModification($element, $modifers);
        }
        return implode(', ', $values);
    }

    /**
     * ?s, ?set
     *
     * @param array $value
     * @param array $modifers
     * @return string
     */
    private function replacement_s(array $value, array $modifers) {
        $set = array();
        foreach ($value as $col => $element) {
            $key   = $this->implementation->reprCol($this->connection, $col);
            $value = $this->valueModification($element, $modifers);
            $set[] = $key.'='.$value;
        }
        return implode(', ', $set);
    }

    /**
     * ?v, ?values
     *
     * @param array $value
     * @param array $modifers
     * @return string
     */
    private function replacement_v(array $value, array $modifers) {
        $values = array();
        foreach ($value as $v) {
            $values[] = '('.$this->replacement_l($v, $modifers).')';
        }
        return implode(', ', $values);
    }

    /**
     * ?t, ?table
     *
     * @param array $value
     * @param array $modifers
     * @return string
     */
    private function replacement_t($value, array $modifers) {
        return $this->implementation->reprTable($this->connection, $this->prefix.$value);
    }

    /**
     * ?c, ?col
     *
     * @param array $value
     * @param array $modifers
     * @return string
     */
    private function replacement_c($value, array $modifers) {
        if (is_array($value)) {
            $chain = array($this->prefix.$value[0], $value[1]);
            $result = $this->implementation->reprChainFields($this->connection, $chain);
        } else {
            $result = $this->implementation->reprCol($this->connection, $value);
        }
        return $result;
    }

    /**
     * ?cols
     *
     * @param array $value
     * @param array $modifers
     * @return string
     */
    private function replacement_xc($value, array $modifers) {
        if (!is_array($value)) {
            return $this->replacement_c($value, $modifers);
        }
        $cols = array();
        foreach ($value as $col) {
            $cols[] = $this->replacement_c($col, $modifers);
        }
        return implode(',', $cols);
    }

    /**
     * ?e, ?escape
     *
     * @param array $value
     * @param array $modifers
     * @return string
     */
    private function replacement_e($value, array $modifers) {
        return $this->implementation->escapeString($this->connection, $value);
    }

    /**
     * ?q, ?query
     *
     * @param array $value
     * @param array $modifers
     * @return string
     */
    private function replacement_q($value, array $modifers) {
        return $value;
    }

    /**
     * Внутренняя реализация взаимодействия с базой
     *
     * @var \go\DB\Implementations\Base
     */
    protected $implementation;

    /**
     * Низкоуровневое подключение к базе
     *
     * @var mixed
     */
    protected $connection;

    /**
     * Шаблон запроса
     *
     * @var string
     */
    protected $pattern;

    /**
     * Входные данные
     *
     * @var array
     */
    protected $data;

    /**
     * Префикс таблиц для данного запроса
     *
     * @var string
     */
    protected $prefix;

    /**
     * Итоговый запрос
     *
     * @var string
     */
    protected $query;

    /**
     * Счётчик обработанных плейсхолдеров
     *
     * @var int
     */
    protected $counter = 0;

    /**
     * Именованные плейсхолдеры используются в запросе или нет
     *
     * @var bool
     */
    protected $named = false;
}