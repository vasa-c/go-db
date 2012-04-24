<?php
/**
 * Исключение: попытка работать с "жестко" закрытым подключением
 *
 * @package    go\DB
 * @subpackage Exceptions
 * @link       https://github.com/vasa-c/go-db/wiki/Exceptions
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