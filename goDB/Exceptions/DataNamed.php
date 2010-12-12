<?php
/**
 * Исключение: нет запрашиваемого именованного данного
 *
 * @package    go\DB
 * @subpackage Exceptions
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\DB\Exceptions;

final class DataNamed extends Data
{
    public function __construct($name) {
        $message = 'Named data "'.$name.'" is not found';
        parent::__construct($message);
    }
}