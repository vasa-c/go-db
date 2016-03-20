<?php
/**
 * @package go\DB
 */

namespace go\DB\Exceptions;

/**
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
final class DataInvalidFormat extends Data
{
    /**
     * @param int $placeholder
     * @param int $message
     */
    public function __construct($placeholder, $message)
    {
        $message = 'Data for ?'.$placeholder.' has invalid format: "'.$message.'"';
        parent::__construct($message);
    }
}
