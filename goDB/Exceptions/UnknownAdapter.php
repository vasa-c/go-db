<?php
/**
 * Исключение: ошибка конфигурации - неизвестный адаптер (или не указан)
 *
 * @package    go\DB
 * @subpackage Exceptions
 * @link       https://github.com/vasa-c/go-db/wiki/Exceptions
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\DB\Exceptions;

final class UnknownAdapter extends Config
{
    /**
     * Конструктор
     * @param string $adapter
     *        название адаптера
     */
    public function __construct($adapter) {
        if ($adapter) {
            $message = 'Unknown adapter "'.$adapter.'"';
            $code    = 1;
        } else {
            $message = 'Not specified adapter';
            $code    = 0;
        }
        parent::__construct($message, $code);
    }
}