<?php
/**
 * Исключение: неизвестный формат разбора
 *
 * @package    go\DB
 * @subpackage Exceptions
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\DB\Exceptions;

final class UnknownFetch extends Fetch
{
    protected $MESSAGE_PATTERN = 'Unknown fetch format "{{ fetch }}"';
}