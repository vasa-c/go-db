<?php
/**
 * Шаблонизатор запроса
 *
 * По шаблону и входящим данным формирует итоговый запрос
 *
 * @package    go\DB
 * @subpackage Helpers
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\DB\Helpers;

use go\DB\Implementations\Base as Implementation;
use go\DB\Exceptions as Exceptions;

class Templater
{
    /**
     * Конструктор
     *
     * @param Implementation $implementation
     *        низкоуровневая реализация подключения к базе
     * @param string $pattern
     *        шаблон запроса
     * @param array $data
     *        входные данные
     * @param string $prefix
     *        префикс запроса
     */
    public function __construct(Implementation $implementation, $pattern, $data, $prefix) {
        
    }

    /**
     * Шаблонизация запроса
     *
     * @throws go\DB\Exceptions\Templater
     *         ошибки при шаблонизации
     *
     * @return string
     *         итоговые запрос
     */
    public function parse() {

    }

    /**
     * Получить итоговый запрос
     *
     * @return string
     */
    public function getQuery() {
        
    }
}