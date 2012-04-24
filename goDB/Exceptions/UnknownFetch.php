<?php
/**
 * Исключение: неизвестный формат разбора
 *
 * @package    go\DB
 * @subpackage Exceptions
 * @link       https://github.com/vasa-c/go-db/wiki/Exceptions
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\DB\Exceptions;

final class UnknownFetch extends Fetch
{
    protected $MESSAGE_PATTERN = 'Unknown fetch format "{{ fetch }}"';
}