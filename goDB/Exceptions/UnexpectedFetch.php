<?php
/**
 * Исключение: неожиданный формат разбора (например, "assoc" для INSERT)
 *
 * @package    go\DB
 * @subpackage Exceptions
 * @link       http://code.google.com/p/go-db/wiki/Exceptions
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\DB\Exceptions;

final class UnexpectedFetch extends Fetch
{
    protected $MESSAGE_PATTERN = 'Unexpected format "{{ fetch }}" for this context';
}