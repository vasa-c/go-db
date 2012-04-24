<?php
/**
 * Базовое Logic-исключение при работе с библиотекой
 *
 * @package    go\DB
 * @subpackage Exceptions
 * @link       https://github.com/vasa-c/go-db/wiki/Exceptions
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\DB\Exceptions;

abstract class Logic extends \LogicException implements Exception
{

}