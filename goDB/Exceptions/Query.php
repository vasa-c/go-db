<?php
/**
 * Исключение: ошибка в запросе
 *
 * @package    go\DB
 * @subpackage Exceptions
 * @link       https://github.com/vasa-c/go-db/wiki/Exceptions
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\DB\Exceptions;

final class Query extends Logic
{
    const MESSAGE_PATTERN = 'Error SQL "{{ query }}"; error="{{ error }}" [#{{ code }}]';

    /**
     * Конструктор
     *
     * @param string $query
     *        ошибочный запрос
     * @param string $error
     *        описание ошибки
     * @param string $errorcode [optional]
     *        код ошибки
     */
    public function __construct($query, $error, $errorcode = null) {
        $this->query = $query;
        $this->error = $error;
        $message = str_replace(
            array('{{ query }}', '{{ error }}', '{{ code }}'),
            array($query, $error, $errorcode),
            self::MESSAGE_PATTERN
        );
        parent::__construct($message, (int)$errorcode);
    }

    /**
     * Получить ошибочный запрос
     *
     * @return string
     */
    public function getQuery() {
        return $this->query;
    }

    /**
     * Получить описание ошибки
     *
     * @return string
     */
    public function getError() {
        return $this->error;
    }

    /**
     * Получить код ошибки
     *
     * @return mixed
     */
    public function getErrorCode() {
        return $this->errorcode;
    }

    private $query;

    private $error;

    private $errorcode;
}