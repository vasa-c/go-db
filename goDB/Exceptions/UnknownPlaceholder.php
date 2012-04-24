<?php
/**
 * Исключение: неизвестный плейсхолдер
 *
 * @package    go\DB
 * @subpackage Exceptions
 * @link       https://github.com/vasa-c/go-db/wiki/Exceptions
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\DB\Exceptions;

final class UnknownPlaceholder extends Placeholder
{
    protected $MESSAGE_PATTERN = 'Unknown placeholder "{{ placeholder }}"';
}