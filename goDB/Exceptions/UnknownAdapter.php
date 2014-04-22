<?php
/**
 * @package go\DB
 */

namespace go\DB\Exceptions;

/**
 * Error: an adapter is unknown
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
final class UnknownAdapter extends Config
{
    /**
     * The constructor
     *
     * @param string $adapter
     */
    public function __construct($adapter)
    {
        if ($adapter) {
            $message = 'Unknown adapter "'.$adapter.'"';
            $code = 1;
        } else {
            $message = 'Not specified adapter';
            $code = 0;
        }
        parent::__construct($message, $code);
    }
}
