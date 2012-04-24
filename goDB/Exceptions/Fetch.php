<?php
/**
 * Исключение: ошибка при разборе результата
 *
 * @package    go\DB
 * @subpackage Exceptions
 * @link       https://github.com/vasa-c/go-db/wiki/Exceptions
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\DB\Exceptions;

abstract class Fetch extends Logic
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
     * @param string $fetch
     *        формат разбора на котором случилась ошибка
     */
    public function __construct($fetch) {
        $message = str_replace(
            '{{ fetch }}',
            $fetch,
            $this->MESSAGE_PATTERN
        );
        $this->fetch = $fetch;
        parent::__construct($message);
    }

    /**
     * Получить ошибочный формат разбора
     *
     * @return string
     */
    public function getFetch() {
        return $this->fetch;
    }

    protected $fetch;
}