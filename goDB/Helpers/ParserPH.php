<?php
/**
 * @package go\DB
 */

namespace go\DB\Helpers;

use go\DB\Compat;
use go\DB\Exceptions\UnknownPlaceholder;

/**
 * The parser of placeholders
 *
 * Extracts parameters from a placeholder.
 * For example, placeholder "?list-null:name;"
 * This class works with a placeholder type only ("list-null")
 * @example
 * <code>
 * $parser = new \go\DB\Templaters\Helpers\ParserPH("list-null");
 * $parser->getType(); // a type in the short form ("l")
 * $parser->getModifers(); // values of modifers: array('n' => true, 'i' => false, ...)
 * </code>
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
final class ParserPH
{
    /**
     * The constructor
     *
     * @param string $placeholder
     *        the placeholder
     * @throws \go\DB\Exceptions\UnknownPlaceholder
     *         the placeholder is unknown
     */
    public function __construct($placeholder)
    {
        $this->placeholder = $placeholder;
        $this->parse();
        if (!$this->modifers['n']) {
            if (Compat::getOpt('null')) {
                $this->modifers['n'] = true;
            }
        }
    }

    /**
     * Returns type of the placeholder
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns values of modifiers
     *
     * @return array
     */
    public function getModifers()
    {
        return $this->modifers;
    }

    /**
     * Parses the placeholder
     */
    private function parse()
    {
        if (!self::$placeholders) {
            self::loadConfig();
        }
        $this->type = '';
        $this->modifers = self::$lModifers;
        $ph = $this->placeholder;
        if ($ph == '') {
            return true;
        }
        $comp = \explode('-', $ph);
        if (\count($comp) > 1) {
            $type = \array_shift($comp);
            if (isset(self::$longs[$type])) {
                $this->type = self::$longs[$type];
            } elseif (isset(self::$longModifers[$type])) {
                $this->modifers[self::$longModifers[$type]] = true;
            } else {
                return $this->error();
            }
            foreach ($comp as $c) {
                if (isset(self::$longModifers[$c])) {
                    $this->modifers[self::$longModifers[$c]] = true;
                } else {
                    return $this->error();
                }
            }
            return true;
        }
        if (isset(self::$longs[$ph])) {
            $this->type = self::$longs[$ph];
            return true;
        }
        if (isset(self::$longModifers[$ph])) {
            $this->modifers[self::$longModifers[$ph]] = true;
            return true;
        }
        $type = $ph[0];
        if (isset(self::$placeholders[$type])) {
            $this->type = $type;
        } elseif (isset(self::$lModifers[$type])) {
            $this->modifers[$type] = true;
        } else {
            return $this->error();
        }
        $len = strlen($ph);
        for ($i = 1; $i < $len; $i++) {
            $modifer = $ph[$i];
            if (isset(self::$lModifers[$modifer])) {
                $this->modifers[$modifer] = true;
            } else {
                return $this->error();
            }
        }
        return true;
    }

    /**
     * Throws a exception for an invalid placeholder
     *
     * @throws \go\DB\Exceptions\UnknownPlaceholder
     */
    private function error()
    {
        throw new UnknownPlaceholder($this->placeholder);
    }

    /**
     * Loads the placeholders configuration
     */
    private static function loadConfig()
    {
        $config = Config::get('placeholders');
        self::$longs = $config['longs'];
        self::$longModifers = $config['longModifers'];
        self::$placeholders = array();
        foreach ($config['placeholders'] as $placeholder) {
            self::$placeholders[$placeholder] = true;
        }
        self::$lModifers = array();
        foreach ($config['modifers'] as $modifer) {
            self::$lModifers[$modifer] = false;
        }
        return true;
    }

    /**
     * The placeholder for parsing
     *
     * @var string
     */
    private $placeholder;

    /**
     * The placeholder type
     *
     * @var string
     */
    private $type;

    /**
     * The placeholder modifiers list
     *
     * @var array
     */
    private $modifers;

    /**
     * @var array
     */
    private static $placeholders;

    /**
     * @var array
     */
    private static $longs;

    /**
     * @var array
     */
    private static $lModifers;

    /**
     * @var array
     */
    private static $longModifers;
}
