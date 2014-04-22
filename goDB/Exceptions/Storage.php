<?php
/**
 * @package go\DB
 */

namespace go\DB\Exceptions;

/**
 * The storage error
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
abstract class Storage extends Logic
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
     * @param string $dbname
     */
    public function __construct($dbname)
    {
        $message = str_replace(
            '{{ dbname }}',
            $dbname,
            $this->MESSAGE_PATTERN
        );
        $this->dbname = $dbname;
        parent::__construct($message);
    }

    /**
     * @return string
     */
    public function getDBName()
    {
        return $this->dbname;
    }

    /**
     * @var string
     */
    protected $dbname;
}
