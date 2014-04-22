<?php
/**
 * @package go\DB
 */

namespace go\DB\Exceptions;

/**
 * Error in a query
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
final class Query extends Logic
{
    /**
     * The error message pattern
     *
     * @var string
     */
    const MESSAGE_PATTERN = 'Error SQL "{{ query }}"; error="{{ error }}" [#{{ code }}]';

    /**
     * The constructor
     *
     * @param string $query
     *        the invalid query
     * @param string $error
     *        the error description
     * @param string $errorcode [optional]
     *        the error code
     */
    public function __construct($query, $error, $errorcode = null)
    {
        $this->query = $query;
        $this->error = $error;
        $this->errorcode = $errorcode;
        $message = str_replace(
            array('{{ query }}', '{{ error }}', '{{ code }}'),
            array($query, $error, $errorcode),
            self::MESSAGE_PATTERN
        );
        parent::__construct($message, (int)$errorcode);
    }

    /**
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return mixed
     */
    public function getErrorCode()
    {
        return $this->errorcode;
    }

    /**
     * @var string
     */
    private $query;

    /**
     * @var string
     */
    private $error;

    /**
     * @var int
     */
    private $errorcode;
}
