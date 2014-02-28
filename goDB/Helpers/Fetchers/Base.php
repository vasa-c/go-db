<?php
/**
 * @package go\Db
 */

namespace go\DB\Helpers\Fetchers;

use go\DB\Result;
use go\DB\Helpers\Config;
use go\DB\Exceptions\UnknownFetch;

/**
 * Basic class of Result implementations
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
abstract class Base implements Result
{
    /**
     * Desctuctor
     */
    public function __destruct()
    {
        $this->free();
    }

    /**
     * {@inheritdoc}
     */
    public function fetch($fetch)
    {
        $fetch = \explode(':', $fetch, 2);
        $param = isset($fetch[1]) ? \strtolower($fetch[1]) : null;
        $fetch = $fetch[0];
        $fetches = Config::get('fetch');
        if (!isset($fetches[$fetch])) {
            throw new UnknownFetch($fetch);
        }
        return $this->$fetch($param);
    }
}
