<?php
/**
 * Исключение: попытка работать с "жестко" закрытым подключением
 *
 * @package    go\DB
 * @subpackage Exceptions
 * @link       http://code.google.com/p/go-db/wiki/Exceptions
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\DB\Exceptions;

final class Closed extends Logic
{
    /**
     * Конструктор
     */
    public function __construct() {
        $message = 'Connection is closed';
        parent::__construct($message, 0);
    }

}