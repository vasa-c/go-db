<?php
/**
 * Исключение: данных меньше чем нужно
 *
 * @package    go\DB
 * @subpackage Exceptions
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\DB\Exceptions;

final class DataNotEnough extends Data
{
    public function __construct($datas, $placeholders) {
        $message = 'Data elements ('.$datas.') less than the placeholders';
        parent::__construct($message);
    }
}