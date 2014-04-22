<?php
/**
 * @package go\DB
 */

namespace go\DB\Exceptions;

/**
 * Error: elements of incoming data more than necessary
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
final class DataMuch extends Data
{
    /**
     * The constructor
     *
     * @param int $datas
     * @param int $placeholders
     */
    public function __construct($datas, $placeholders)
    {
        $message = 'Data elements ('.$datas.') more than the placeholders ('.$placeholders.')';
        parent::__construct($message);
    }
}
