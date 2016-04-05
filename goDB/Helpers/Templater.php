<?php
/**
 * @package go\DB
 */

namespace go\DB\Helpers;

use go\DB\Exceptions\DataMuch;
use go\DB\Exceptions\DataNotEnough;
use go\DB\Exceptions\DataNamed;
use go\DB\Exceptions\DataInvalidFormat;
use go\DB\Exceptions\UnknownPlaceholder;
use go\DB\Exceptions\MixedPlaceholder;

/**
 * The query templating system
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
class Templater
{
    /**
     * The constructor
     *
     * @param \go\DB\Helpers\Connector $connector
     *        a database connector (a connection must be established)
     * @param string $pattern
     *        a query pattern
     * @param array $data
     *        an incoming data for the pattern
     * @param string $prefix
     *        a prefix for tables
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
     * The query templating
     *
     * @return string
     *         the result query
     * @throws \go\DB\Exceptions\Templater
     */
    public function parse()
    {
        if ($this->query !== null) {
            return $this->query;
        }
        $query = preg_replace_callback('~{(.*?)}~', array($this, 'tableClb'), $this->pattern);
        $pattern = '~\?([a-z\?-]+)?(:([a-z0-9_-]*))?;?~i';
        $callback = array($this, 'placeholderClb');
        $query = preg_replace_callback($pattern, $callback, $query);
        if ((!$this->named) && (count($this->data) > $this->counter)) {
            if (($this->counter > 0) || (isset($this->data[0]))) {
                throw new DataMuch(count($this->data), $this->counter);
            }
        }
        $this->query = $query;
        return $this->query;
    }

    /**
     * Returns the result query
     *
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Replaces "{table}" to a table name
     *
     * @param array $matches
     * @return string
     */
    private function tableClb($matches)
    {
        return $this->implementation->reprTable($this->connection, $this->prefix.$matches[1]);
    }

    /**
     * Replaces a next placeholder in the pattern
     *
     * @param array $matches
     * @return string
     * @throws \go\DB\Exceptions\Templater
     */
    protected function placeholderClb($matches)
    {
        $placeholder = isset($matches[1]) ? $matches[1] : '';
        if (isset($matches[3])) {
            $name = $matches[3];
            if (empty($name)) {
                /* There is a named placeholder without name ("?set:") */
                throw new UnknownPlaceholder($matches[0]);
            }
        } else {
            $name = null;
            if ($placeholder == '?') { // "??" for question mark
                return '?';
            }
        }
        if ($name) {
            if ($this->counter == 0) {
                $this->named = true;
            } elseif (!$this->named) {
                /* There is a named placeholder although already used regular */
                throw new MixedPlaceholder($matches[0]);
            }
            if (!array_key_exists($name, $this->data)) {
                throw new DataNamed($name);
            }
            $value = $this->data[$name];
        } elseif ($this->named) {
            /* There is a regular placeholder although already used named */
            throw new MixedPlaceholder($matches[0]);
        } else {
            if (!array_key_exists($this->counter, $this->data)) {
                /* Data for regular placeholders is ended */
                throw new DataNotEnough(count($this->data), $this->counter);
            }
            $value = $this->data[$this->counter];
        }
        $this->counter++;
        $parser = new ParserPH($placeholder);
        $type = $parser->getType();
        $modifiers = $parser->getModifiers();
        $method = 'replacement'.strtoupper($type);
        return $this->$method($value, $modifiers);
    }

    /**
     * Converts a scalar value in conformity with the modifiers list
     *
     * @param mixed $value
     * @param array $modifiers
     * @return string
     */
    private function valueModification($value, array $modifiers)
    {
        if ($modifiers['n'] && is_null($value)) {
            return $this->implementation->reprNULL($this->connection);
        }
        if ($modifiers['i']) {
            return $this->implementation->reprInt($this->connection, $value);
        } elseif ($modifiers['f']) {
            return $this->implementation->reprFloat($this->connection, $value);
        } elseif ($modifiers['b']) {
            return $this->implementation->reprBool($this->connection, $value);
        }
        return $this->implementation->reprString($this->connection, $value);
    }

    /**
     * ?, ?string, ?scalar
     *
     * @param mixed $value
     * @param array $modifiers
     * @return string
     */
    protected function replacement($value, array $modifiers)
    {
        if (is_array($value)) {
            throw new DataInvalidFormat('', 'required scalar given');
        }
        return $this->valueModification($value, $modifiers);
    }

    /**
     * ?l, ?list
     *
     * @param array $value
     * @param array $modifiers
     * @return string
     */
    protected function replacementL($value, array $modifiers)
    {
        if (!is_array($value)) {
            throw new DataInvalidFormat('list', 'required array (list of values)');
        }
        $values = array();
        foreach ($value as $k => $element) {
            if (is_array($element)) {
                throw new DataInvalidFormat('list', 'required scalar in item #'.$k);
            }
            $values[] = $this->valueModification($element, $modifiers);
        }
        return implode(', ', $values);
    }

    /**
     * ?s, ?set
     *
     * @param array $value
     * @param array $modifiers
     * @return string
     */
    protected function replacementS($value, array $modifiers)
    {
        if (!is_array($value)) {
            throw new DataInvalidFormat('set', 'required array (column => value)');
        }
        $set = array();
        foreach ($value as $col => $element) {
            $key = $this->implementation->reprCol($this->connection, $col);
            if (is_array($element)) {
                if (empty($element)) {
                    $element = 'NULL';
                } else {
                    $element = $this->replacementC($element, $modifiers);
                }
            } else {
                if (is_int($element)) {
                    $element = $this->implementation->reprInt($this->connection, $element);
                } else {
                    $element = $this->valueModification($element, $modifiers);
                }
            }
            $set[] = $key.'='.$element;
        }
        return implode(', ', $set);
    }

    /**
     * ?v, ?values
     *
     * @param array $value
     * @param array $modifiers
     * @return string
     */
    private function replacementV($value, array $modifiers)
    {
        if (!is_array($value)) {
            throw new DataInvalidFormat('values', 'required array of arrays');
        }
        $values = array();
        foreach ($value as $v) {
            $values[] = '('.$this->replacementL($v, $modifiers).')';
        }
        return implode(', ', $values);
    }

    /**
     * ?t, ?table
     *
     * @param array $value
     * @param array $modifiers
     * @return string
     */
    private function replacementT($value, array $modifiers)
    {
        if (!is_array($value)) {
            return $this->implementation->reprTable($this->connection, $this->prefix.$value);
        }
        if (isset($value[0])) {
            $chain = $value;
        } elseif (isset($value['table'])) {
            if (is_array($value['table'])) {
                $chain = $value['table'];
            } else {
                $chain = array($value['table']);
            }
            if (isset($value['db'])) {
                array_unshift($chain, $value['db']);
            }
        } elseif (!isset($value['table'])) {
            throw new DataInvalidFormat('t', 'required `table` field');
        }
        $lastIdx = count($chain) - 1;
        $chain[$lastIdx] = $this->prefix.$chain[$lastIdx];
        $result = $this->implementation->reprChainFields($this->connection, $chain);
        if (isset($value['as'])) {
            $result .= ' AS '.$this->implementation->reprCol($this->connection, $value['as']);
        }
        return $result;
    }

    /**
     * ?c, ?col
     *
     * @param array $value
     * @param array $modifiers
     * @return string
     */
    private function replacementC($value, array $modifiers)
    {
        if (!is_array($value)) {
            return $this->implementation->reprCol($this->connection, $value);
        }
        if (isset($value[0])) {
            if ($this->prefix !== null) {
                $t = count($value) - 2;
                if (isset($value[$t])) {
                    $value[$t] = $this->prefix . $value[$t];
                }
            }
            return $this->implementation->reprChainFields($this->connection, $value);
        }
        if (isset($value['col'])) {
            $chain = array();
            foreach (array('db', 'table', 'col') as $f) {
                if (isset($value[$f])) {
                    if (is_array($value[$f])) {
                        $chain = array_merge($chain, $value[$f]);
                    } else {
                        $chain[] = $value[$f];
                    }
                }
            }
            if ($this->prefix !== null) {
                $t = count($chain) - 2;
                if (isset($chain[$t])) {
                    $chain[$t ] = $this->prefix.$chain[$t];
                }
            }
            $result = $this->implementation->reprChainFields($this->connection, $chain);
        } elseif (isset($value['value'])) {
            if (is_int($value['value'])) {
                $result = $this->implementation->reprInt($this->connection, $value['value']);
            } else {
                $result = $this->implementation->reprString($this->connection, $value['value']);
            }
            if (array_key_exists('col', $value) && isset($value['func'])) {
                $result = '';
            } else {
                $value['value'] = null;
            }
        } elseif (isset($value['func'])) {
            $result = '';
        } else {
            throw new DataInvalidFormat('col', 'required `col`, `value` or `func` field');
        }
        if (isset($value['func'])) {
            $result = $value['func'].'('.$result.')';
        }
        if (isset($value['value'])) {
            $result .= (($value['value'] > 0) ? '+' : '').(int)$value['value'];
        }
        if (isset($value['as'])) {
            $result .= ' AS '.$this->implementation->reprCol($this->connection, $value['as']);
        }
        return $result;
    }

    /**
     * ?cols
     *
     * @param array $value
     * @param array $modifiers
     * @return string
     */
    private function replacementXC($value, array $modifiers)
    {
        if (!is_array($value)) {
            if ($value === true) {
                return '*';
            }
            return $this->replacementC($value, $modifiers);
        }
        if (empty($value)) {
            return '*';
        }
        $cols = array();
        foreach ($value as $col) {
            $cols[] = $this->replacementC($col, $modifiers);
        }
        return implode(',', $cols);
    }

    /**
     * ?e, ?escape
     *
     * @param array $value
     * @param array $modifiers
     * @return string
     */
    private function replacementE($value, array $modifiers)
    {
        if (is_array($value)) {
            throw new DataInvalidFormat('escape', 'required string');
        }
        return $this->implementation->escapeString($this->connection, $value);
    }

    /**
     * ?q, ?query
     *
     * @param array $value
     * @param array $modifiers
     * @return string
     */
    private function replacementQ($value, array $modifiers)
    {
        if (is_array($value)) {
            throw new DataInvalidFormat('query', 'required string');
        }
        return $value;
    }

    /**
     * ?w, ?where
     *
     * @param mixed $value
     * @param array $modifiers
     * @return string
     */
    private function replacementW($value, array $modifiers)
    {
        return $this->whereGroup($value, $modifiers);
    }

    /**
     * ?o, ?order
     *
     * @param mixed $value
     * @param array $modifiers
     * @return string
     */
    private function replacementO($value, array $modifiers)
    {
        if (!is_array($value)) {
            return $this->replacementC($value, $modifiers).' ASC';
        }
        $stats = array();
        foreach ($value as $k => $v) {
            if (is_int($k)) {
                $c = $v;
                $s = 'ASC';
            } else {
                $c = $k;
                $s = $v ? 'ASC' : 'DESC';
            }
            $stats[] = $this->replacementC($c, $modifiers).' '.$s;
        }
        return implode(',', $stats);
    }

    /**
     * @param mixed $value
     * @param array $modifiers
     * @param string $sep [optional]
     * @return string
     */
    private function whereGroup($value, array $modifiers, $sep = 'AND')
    {
        if (!is_array($value)) {
            return ($value !== false) ? '1=1' : '1=0';
        }
        $stats = array();
        foreach ($value as $k => $v) {
            $col = $this->replacementC($k, $modifiers);
            if (is_array($v)) {
                if (empty($v)) {
                    return '1=0';
                }
                if (isset($v[0])) {
                    $opts = array();
                    foreach ($v as $opt) {
                        if (is_int($opt)) {
                            $opts[] = $opt;
                        } else {
                            $opts[] = $this->replacement($opt, $modifiers);
                        }
                    }
                    $stat = $col.' IN ('.implode(',', $opts).')';
                } elseif (isset($v['group'])) {
                    $sepG = isset($v['sep']) ? $v['sep'] : 'AND';
                    $stat = '('.$this->whereGroup($v['group'], $modifiers, $sepG).')';
                } else {
                    $op = isset($v['op']) ? $v['op'] : '=';
                    $stat = $col.$op.$this->replacementC($v, $modifiers);
                }
            } elseif ($v === null) {
                $stat = $col.' IS NULL';
            } elseif ($v === true) {
                $stat = $col.' IS NOT NULL';
            } elseif (is_int($v)) {
                $stat = $col.'='.$v;
            } else {
                $stat = $col.'='.$this->replacement($v, $modifiers);
            }
            $stats[] = $stat;
        }
        if (empty($stats)) {
            return '1=1';
        }
        return implode(' '.$sep.' ', $stats);
    }

    /**
     * @var \go\DB\Implementations\Base
     */
    protected $implementation;

    /**
     * @var mixed
     */
    protected $connection;

    /**
     * @var string
     */
    protected $pattern;

    /**
     * @var array
     */
    protected $data;

    /**
     * @var string
     */
    protected $prefix;

    /**
     * @var string
     */
    protected $query;

    /**
     * @var int
     */
    protected $counter = 0;

    /**
     * @var bool
     */
    protected $named = false;
}
