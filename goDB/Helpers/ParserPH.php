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
        $this->placeholder = $placeholder;
        $this->parse();
    }

    /**
     * Получить тип плейсхолдера
     *
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Получить настройки модификаторов
     *
     * @return array
     */
    public function getModifers() {
        return $this->modifers;
    }

    /**
     * Разбор плейсхолдера
     */
    private function parse() {
        if (!self::$placeholders) {
            self::loadConfig();
        }
        $this->type = '';
        $this->modifers = self::$lModifers;
        $ph = $this->placeholder;
        if ($ph == '') {
            return true;
        }
        $comp = explode('-', $ph);
        if (count($comp) > 1) {
            $type = array_shift($comp);
            if (isset(self::$longs[$type])) {
                $this->type = self::$longs[$type];
            } elseif (isset(self::$longModifers[$type])) {
                $this->modifers[self::$longModifers[$type]] = true;
            } else {
                return $this->error();
            }
            foreach ($comp as $c) {
                if (isset(self::$longModifers[$c])) {
                    $this->modifers[self::$longModifers[$c]] = true;
                } else {
                    return $this->error();
                }
            }
            return true;
        }
        if (isset(self::$longs[$ph])) {
            $this->type = self::$longs[$ph];
            return true;
        }
        if (isset(self::$longModifers[$ph])) {
            $this->modifers[self::$longModifers[$ph]] = true;
            return true;
        }
        $type = $ph[0];
        if (isset(self::$placeholders[$type])) {
            $this->type = $type;
        } elseif (isset(self::$lModifers[$type])) {
            $this->modifers[$type] = true;
        } else {
            return $this->error();
        }
        $len = strlen($ph);
        for ($i = 1; $i < $len; $i++) {
            $modifer = $ph[$i];
            if (isset(self::$lModifers[$modifer])) {
                $this->modifers[$modifer] = true;
            } else {
                return $this->error();
            }
        }
        return true;
    }

    /**
     * Выброс исключения об ошибочном плейсхолдере
     *
     * @throws \go\DB\Exceptions\UnknownPlaceholder
     */
    private function error() {
        throw new \go\DB\Exceptions\UnknownPlaceholder($this->placeholder);
    }

    /**
     * Загрузить конфигурацию плейсхолдеров
     */
    private static function loadConfig() {
        $config = Config::get('placeholders');
        self::$longs = $config['longs'];
        self::$longModifers = $config['longModifers'];
        self::$placeholders = array();
        foreach ($config['placeholders'] as $placeholder) {
            self::$placeholders[$placeholder] = true;
        }
        self::$lModifers = array();
        foreach ($config['modifers'] as $modifer) {
            self::$lModifers[$modifer] = false;
        }
        return true;
    }

    /**
     * Разбираемый плейсхолдер
     *
     * @var string
     */
    private $placeholder;

    /**
     * Тип плейсхолдера
     *
     * @var string
     */
    private $type;

    /**
     * Настройки модификаторов
     *
     * @var array
     */
    private $modifers;

    /**
     * Список доступных плейсхолдеров ("имя" => true)
     *
     * @var array
     */
    private static $placeholders;

    /**
     * Список длинных синонимов ("длинный" => "короткий")
     *
     * @var array
     */
    private static $longs;

    /**
     * Список модификаторов ("имя" => false)
     *
     * @var array
     */
    private static $lModifers;

    /**
     * Список длинных синонимов модифакторов ("динный" => "короткий")
     *
     * @var array
     */
    private static $longModifers;
}