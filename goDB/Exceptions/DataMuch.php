<?php
/**
 * Исключение: данных больше чем нужно
 *
 * @package    go\DB
 * @subpackage Exceptions
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\DB\Exceptions;

final class DataMuch extends Data
{
    public function __construct($datas, $placeholders) {
        $message = 'Data elements ('.$datas.') more than the placeholders ('.$placeholders.')';
        parent::__construct($message);
    }
}