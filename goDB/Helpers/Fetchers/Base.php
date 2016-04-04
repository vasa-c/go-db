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
     * Destructor
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
        $params = explode(':', $fetch);
        $method = array_shift($params);
        $fetches = Config::get('fetch');
        if (!isset($fetches[$method])) {
            throw new UnknownFetch($method);
        }
        return call_user_func_array(array($this, $method), $params);
    }
}
