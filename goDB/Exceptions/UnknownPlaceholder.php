<?php
/**
 * Исключение: неизвестный плейсхолдер
 *
 * @package    go\DB
 * @subpackage Exceptions
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\DB\Exceptions;

final class UnknownPlaceholder extends Placeholder
{
    protected $MESSAGE_PATTERN = 'Unknown placeholder "{{ placeholder }}"';
}