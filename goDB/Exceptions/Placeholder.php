<?php
/**
 * Исключение: ошибка плейсхолдера
 *
 * @package    go\DB
 * @subpackage Exceptions
 * @link       https://github.com/vasa-c/go-db/wiki/Exceptions
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\DB\Exceptions;

abstract class Placeholder extends Templater
{
    /**
     * Шаблон сообщения, переопределяется у потомков
     *
     * @var string
     */
    protected $MESSAGE_PATTERN;

    /**
     * Конструктор
     *
     * @param string $placeholder
     *        плейсхолдер на котором случилась ошибка
     */
    public function __construct($placeholder) {
        $message = str_replace(
            '{{ placeholder }}',
            $placeholder,
            $this->MESSAGE_PATTERN
        );
        $this->placeholder = $placeholder;
        parent::__construct($message);
    }

    /**
     * Получить ошибочный плейсхолдер
     *
     * @return string
     */
    public function getPlaceholder() {
        return $this->placeholder;
    }

    protected $placeholder;
}