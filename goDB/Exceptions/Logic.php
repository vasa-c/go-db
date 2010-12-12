<?php
/**
 * Базовое Logic-исключение при работе с библиотекой
 *
 * @package    go\DB
 * @subpackage Exceptions
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\DB\Exceptions;

abstract class Logic extends \LogicException implements Exception
{

}