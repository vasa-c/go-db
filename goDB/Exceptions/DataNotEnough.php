<?php
/**
 * @package go\DB
 */

namespace go\DB\Exceptions;

/**
 * Error: elements of incoming data less than necessary
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
final class DataNotEnough extends Data
{
    public function __construct($datas, $placeholders)
    {
        $message = 'Data elements ('.$datas.') less than the placeholders';
        parent::__construct($message);
    }
}
