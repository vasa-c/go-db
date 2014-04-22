<?php
/**
 * @package go\DB
 */

namespace go\DB\Exceptions;

/**
 * Error: a result format is invalid
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
abstract class Fetch extends Logic
{
    /**
     * The template for the error message
     *
     * @var string
     */
    protected $MESSAGE_PATTERN;

    /**
     * The constructor
     *
     * @param string $fetch
     */
    public function __construct($fetch)
    {
        $message = str_replace(
            '{{ fetch }}',
            $fetch,
            $this->MESSAGE_PATTERN
        );
        $this->fetch = $fetch;
        parent::__construct($message);
    }

    /**
     * @return string
     */
    public function getFetch()
    {
        return $this->fetch;
    }

    /**
     * @var string
     */
    protected $fetch;
}
