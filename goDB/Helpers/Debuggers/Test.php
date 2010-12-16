<?php
/**
 * Тестовый отладчик
 *
 * @package    go\DB
 * @subpackage Helpers
 * @author     Григорьев Олег aka vasa_c
 */

namespace go\DB\Helpers\Debuggers;

final class Test {

    /**
     * @param string $query
     * @param float $duration
     * @param mixed $info
     */
    public function __invoke($query, $duration, $info) {
        $this->query    = $query;
        $this->duration = $duration;
        $this->info     = $info;
    }

    /**
     * @return string
     */
    public function getLastQuery() {
        return $this->query;
    }

    /**
     * @return float
     */
    public function getLastDuration() {
        return $this->duration;
    }

    /**
     * @return mixed
     */
    public function getLastInfo() {
        return $this->info;
    }

    /**
     * @var string
     */
    private $query;

    /*
     * @var float
     */
    private $duration;

    /**
     * @var mixed
     */
    private $info;
}