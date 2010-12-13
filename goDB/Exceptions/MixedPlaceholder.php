<?php
/**
 * Исключение: смешанные регулярные и именованные плейсхолдеры
 *
 * @package    go\DB
 * @subpackage Exceptions
 * @link       http://code.google.com/p/go-db/wiki/Exceptions
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\DB\Exceptions;

final class MixedPlaceholder extends Placeholder
{
    protected $MESSAGE_PATTERN = 'Mixed placeholder "{{ placeholder }}"';
}