<?php
/**
 * @package go\DB
 */

namespace go\DB\Implementations\TestBase;

/**
 * The cursor of test base
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
final class Cursor
{
    /**
     * The constructor
     *
     * @param array $data
     *        a selected data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
        $this->reset();
    }

    /**
     * fetch_row
     *
     * @return array | false
     */
    public function fetchRow()
    {
        $row = $this->next();
        return $row ? \array_values($row) : false;
    }

    /**
     * fetch_assoc
     *
     * @return array | false
     */
    public function fetchAssoc()
    {
        $row = $this->next();
        return $row ? $row : false;
    }

    /**
     * fetch_object
     *
     * @return array | false
     */
    public function fetchObject()
    {
        $row = $this->next();
        return $row ? (object)$row : false;
    }

    /**
     * Resets the cursor
     */
    public function reset()
    {
        \reset($this->data);
        return true;
    }

    /**
     * Returns a size of the result
     *
     * @return int
     */
    public function getNumRows()
    {
        return \count($this->data);
    }

    /**
     * @return array
     */
    private function next()
    {
        $value = \current($this->data);
        next($this->data);
        return $value;
    }

    /**
     * @var array
     */
    private $data;
}
