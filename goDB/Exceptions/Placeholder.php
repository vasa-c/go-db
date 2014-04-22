<?php
/**
 * @package go\DB
 */

namespace go\DB\Exceptions;

/**
 * Error: a placeholder from the pattern is invalid
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
abstract class Placeholder extends Templater
{
    /**
     * The error message pattern
     *
     * @var string
     */
    protected $MESSAGE_PATTERN;

    /**
     * The constructor
     *
     * @param string $placeholder
     */
    public function __construct($placeholder)
    {
        $message = str_replace(
            '{{ placeholder }}',
            $placeholder,
            $this->MESSAGE_PATTERN
        );
        $this->placeholder = $placeholder;
        parent::__construct($message);
    }

    /**
     * @return string
     */
    public function getPlaceholder()
    {
        return $this->placeholder;
    }

    /**
     * @var string
     */
    protected $placeholder;
}
