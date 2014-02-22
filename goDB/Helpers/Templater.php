<?php
/**
 * Шаблонизатор запроса
 *
 * По шаблону и входящим данным формирует итоговый запрос
 *
 * @package go\DB
 * @subpackage Helpers
 * @author Григорьев Олег aka vasa_c
 */

namespace go\DB\Helpers;

use go\DB\Exceptions;

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
     *        префикс таблиц для данного запроса
     */
    public function __construct(Connector $connector, $pattern, $data, $prefix)
    {
        $this->implementation = $connector->getImplementation();
        $this->connection = $connector->getConnection();
        $this->pattern = $pattern;
        $this->data = $data ?: array();
        $this->prefix = $prefix;
    }

    /**
     * Шаблонизация запроса
     *
     * @return string
     *         итоговые запрос
     * @throws \go\DB\Exceptions\Templater
     *         ошибки при шаблонизации
     */
    public function parse()
    {
        if (!\is_null($this->query)) {
            return $this->query;
        }
        /* Замена {table} */
        $query = \preg_replace_callback('~{(.*?)}~', array($this, 'tableClb'), $this->pattern);
        /* Замена плейсхолдеров */
        $pattern = '~\?([a-z\?-]+)?(:([a-z0-9_-]*))?;?~i';
        $callback = array($this, 'placeholderClb');
        $query = \preg_replace_callback($pattern, $callback, $query);
        if ((!$this->named) && (\count($this->data) > $this->counter)) {
            throw new Exceptions\DataMuch(count($this->data), $this->counter);
        }
        $this->query = $query;
        return $this->query;
    }

    /**
     * Получить итоговый запрос
     *
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Замена имени таблицы "{table}"
     */
    private function tableClb($matches)
    {
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
    private function placeholderClb($matches)
    {
        $placeholder = isset($matches[1]) ? $matches[1] : '';
        if (isset($matches[3])) {
            $name = $matches[3];
            if (empty($name)) {
                /* Именованный плейсхолдер без имени ("?set:") */
                throw new Exceptions\UnknownPlaceholder($matches[0]);
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
                throw new Exceptions\MixedPlaceholder($matches[0]);
            }
            if (!\array_key_exists($name, $this->data)) {
                throw new Exceptions\DataNamed($name);
            }
            $value = $this->data[$name];
        } elseif ($this->named) {
            /* Регулярный плейсхолдер, хотя уже использовались именованные */
            throw new Exceptions\MixedPlaceholder($matches[0]);
        } else {
            if (!\array_key_exists($this->counter, $this->data)) {
                /* Данные для регулярных плейсхолдеров закончились */
                throw new Exceptions\DataNotEnough(count($this->data), $this->counter);
            }
            $value = $this->data[$this->counter];
        }
        $this->counter++;
        $parser = new ParserPH($placeholder);
        $type = $parser->getType();
        $modifers = $parser->getModifers();
        $method = 'replacement'.\strtoupper($type);
        return $this->$method($value, $modifers);
    }

    /**
     * Преобразование скалярного значения в соответствии с модификаторами плейсхолдера
     *
     * @param mixed $value
     * @param array $modifers
     * @return string
     */
    private function valueModification($value, array $modifers)
    {
        if ($modifers['n'] && \is_null($value)) {
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
    private function replacement($value, array $modifers)
    {
        return $this->valueModification($value, $modifers);
    }

    /**
     * ?l, ?list
     *
     * @param array $value
     * @param array $modifers
     * @return string
     */
    private function replacementL(array $value, array $modifers)
    {
        $values = array();
        foreach ($value as $element) {
            $values[] = $this->valueModification($element, $modifers);
        }
        return \implode(', ', $values);
    }

    /**
     * ?s, ?set
     *
     * @param array $value
     * @param array $modifers
     * @return string
     */
    private function replacementS(array $value, array $modifers)
    {
        $set = array();
        foreach ($value as $col => $element) {
            $key = $this->implementation->reprCol($this->connection, $col);
            $value = $this->valueModification($element, $modifers);
            $set[] = $key.'='.$value;
        }
        return \implode(', ', $set);
    }

    /**
     * ?v, ?values
     *
     * @param array $value
     * @param array $modifers
     * @return string
     */
    private function replacementV(array $value, array $modifers)
    {
        $values = array();
        foreach ($value as $v) {
            $values[] = '('.$this->replacementL($v, $modifers).')';
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
    private function replacementT($value, array $modifers)
    {
        return $this->implementation->reprTable($this->connection, $this->prefix.$value);
    }

    /**
     * ?c, ?col
     *
     * @param array $value
     * @param array $modifers
     * @return string
     */
    private function replacementC($value, array $modifers)
    {
        if (\is_array($value)) {
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
    private function replacementXC($value, array $modifers)
    {
        if (!\is_array($value)) {
            if ($value === true) {
                return '*';
            }
            return $this->replacementC($value, $modifers);
        }
        if (empty($value)) {
            return '*';
        }
        $cols = array();
        foreach ($value as $col) {
            $cols[] = $this->replacementC($col, $modifers);
        }
        return \implode(',', $cols);
    }

    /**
     * ?e, ?escape
     *
     * @param array $value
     * @param array $modifers
     * @return string
     */
    private function replacementE($value, array $modifers)
    {
        return $this->implementation->escapeString($this->connection, $value);
    }

    /**
     * ?q, ?query
     *
     * @param array $value
     * @param array $modifers
     * @return string
     */
    private function replacementQ($value, array $modifers)
    {
        return $value;
    }

    /**
     * ?w, ?where
     *
     * @param mixed $value
     * @param array $modifers
     * @return string
     */
    private function replacementW($value, array $modifers)
    {
        if (!\is_array($value)) {
            return ($value !== false) ? '1' : '0';
        }
        $stats = array();
        foreach ($value as $k => $v) {
            $col = $this->replacementC($k, $modifers);
            if (\is_array($v)) {
                if (empty($v)) {
                    break;
                }
                $opts = array();
                foreach ($v as $opt) {
                    if (\is_int($opt)) {
                        $opts[] = $opt;
                    } else {
                        $opts[] = $this->replacement($opt, $modifers);
                    }
                }
                $stat = $col.' IN ('.\implode(',', $opts).')';
            } elseif ($v === null) {
                $stat = $col.' IS NULL';
            } elseif ($v === true) {
                $stat = $col.' IS NOT NULL';
            } elseif (\is_int($v)) {
                $stat = $col.'='.$v;
            } else {
                $stat = $col.'='.$this->replacement($v, $modifers);
            }
            $stats[] = $stat;
        }
        if (empty($stats)) {
            return '1';
        }
        return \implode(' AND ', $stats);
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
