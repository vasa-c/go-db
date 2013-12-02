<?php
/**
 * Исключение: не удалось подключиться к серверу (или выбрать базу)
 *
 * @package    go\DB
 * @subpackage Exceptions
 * @link       https://github.com/vasa-c/go-db/wiki/Exceptions
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\DB\Exceptions;

final class Connect extends Runtime
{
    public function __construct($message, $code = null, $previous = null)
    {
        $backtrace = new Helpers\Backtrace($this->getTrace(), 'go\DB\\');
        $this->realFile = $this->file;
        $this->realLine = $this->line;
        $this->userTrace = $backtrace->getTrace();
        $this->file = $backtrace->getFile();
        $this->line = $backtrace->getLine();
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return array
     */
    final public function getUserTrace()
    {
        return $this->userTrace;
    }

    /**
     * @return string
     */
    final public function getRealFile()
    {
        return $this->realFile;
    }

    /**
     * @return int
     */
    final public function getRealLine()
    {
        return $this->realLine;
    }

    private $userTrace, $realFile, $realLine;
}
