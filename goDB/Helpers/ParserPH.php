<?php
/**
 * Разборщик плейсхолдера
 *
 * Вытаскивает параметры плейсхолдера.
 * Например, плейсхолдер "?list-null:name;"
 *
 * Данный класс работает только с типом плейсхолдера ("list-null"):
 * @example
 * <code>
 * $parser = new \go\DB\Templaters\Helpers\ParserPH("list-null");
 * $parser->getType(); // тип перведённый к краткой форме ("l")
 * $parser->getModifers(); // вкл/выкл модификаторов: array('n' => true, 'i' => false, ...)
 * </code>
 *
 * @package    go\DB
 * @subpackage Helpers
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\DB\Helpers;

final class ParserPH
{
    /**
     * Конструктор
     *
     * @throws \go\DB\Exceptions\UnknownPlaceholder
     *         неизвестный плейсхолдер
     *
     * @param string $placeholder
     *        плейсхолдер
     */
    public function __construct($placeholder) {
    }

    /**
     * Получить тип плейсхолдера
     *
     * @return string
     */
    public function getType() {
    }

    /**
     * Получить настройки модификаторов
     *
     * @return array
     */
    public function getModifers() {
    }
}