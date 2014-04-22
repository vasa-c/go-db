<?php
/**
 * @package go\DB
 */

namespace go\DB\Helpers;

use go\DB\Exceptions\DataMuch;
use go\DB\Exceptions\DataNotEnough;
use go\DB\Exceptions\DataNamed;
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
        $query = \preg_replace_callback('~{(.*?)}~', array($this, 'tableClb'), $this->pattern);
        $pattern = '~\?([a-z\?-]+)?(:([a-z0-9_-]*))?;?~i';
        $callback = array($this, 'placeholderClb');
        $query = \preg_replace_callback($pattern, $callback, $query);
        if ((!$this->named) && (\count($this->data) > $this->counter)) {
            throw new DataMuch(count($this->data), $this->counter);
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
     * @throws \go\DB\Exceptions\Templaters
     */
    private function placeholderClb($matches)
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
            if (!\array_key_exists($name, $this->data)) {
                throw new DataNamed($name);
            }
            $value = $this->data[$name];
        } elseif ($this->named) {
            /* There is a regular placeholder although already used named */
            throw new MixedPlaceholder($matches[0]);
        } else {
            if (!\array_key_exists($this->counter, $this->data)) {
                /* Data for regular placeholders is ended */
                throw new DataNotEnough(count($this->data), $this->counter);
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
     * Converts a scalar value in conformity with the modifiers list
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
            if (\is_array($element)) {
                $oval = isset($element['value']) ? $element['value'] : null;
                if (isset($element['col'])) {
                    $element = $this->replacementC($element['col'], $modifers);
                    if ($oval !== null) {
                        $oval = (int)$oval;
                        if ($oval > 0) {
                            $element .= '+'.$oval;
                        } else {
                            $element .= $oval;
                        }
                    }
                } else {
                    $element = $this->valueModification($oval, $modifers);
                }
            } else {
                $element = $this->valueModification($element, $modifers);
            }
            $set[] = $key.'='.$element;
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
                if (isset($v['op'])) {
                    $stat = $col.$v['op'];
                    $oval = isset($v['value']) ? $v['value'] : null;
                    if (isset($v['col'])) {
                        $stat .= $this->replacementC($v['col'], $modifers);
                        if ($oval) {
                            $oval = (int)$oval;
                            if ($oval > 0) {
                                $stat .= '+'.$oval;
                            } else {
                                $stat .= $oval;
                            }
                        }
                    } else {
                        if ($oval === null) {
                            $stat .= 'NULL';
                        } elseif (\is_int($oval)) {
                            $stat .= $oval;
                        } else {
                            $stat .= $this->replacement($oval, $modifers);
                        }
                    }
                } else {
                    $opts = array();
                    foreach ($v as $opt) {
                        if (\is_int($opt)) {
                            $opts[] = $opt;
                        } else {
                            $opts[] = $this->replacement($opt, $modifers);
                        }
                    }
                    $stat = $col.' IN ('.\implode(',', $opts).')';
                }
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
     * ?o, ?order
     *
     * @param mixed $value
     * @param array $modifers
     * @return string
     */
    private function replacementO($value, array $modifers)
    {
        if (!\is_array($value)) {
            return $this->replacementC($value, $modifers);
        }
        $stats = array();
        foreach ($value as $k => $v) {
            if (\is_int($k)) {
                $c = $v;
                $s = 'ASC';
            } else {
                $c = $k;
                $s = $v ? 'ASC' : 'DESC';
            }
            $stats[] = $this->replacementC($c, $modifers).' '.$s;
        }
        return \implode(',', $stats);
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
