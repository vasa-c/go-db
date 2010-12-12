<?php
/**
 * Исключение: смешанные регулярные и именованные плейсхолдеры
 *
 * @package    go\DB
 * @subpackage Exceptions
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\DB\Exceptions;

final class MixedPlaceholder extends Placeholder
{
    protected $MESSAGE_PATTERN = 'Mixed placeholder "{{ placeholder }}"';
}