<?php
/**
 * Исключение: неожиданный формат разбора (например, "assoc" для INSERT)
 *
 * @package    go\DB
 * @subpackage Exceptions
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\DB\Exceptions;

final class UnexpectedFetch extends Fetch
{
    protected $MESSAGE_PATTERN = 'Unexpected format "{{ fetch }}" for this context';
}