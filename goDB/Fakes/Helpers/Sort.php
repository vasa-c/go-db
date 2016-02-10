<?php
/**
 * @package go\DB
 */

namespace go\DB\Fakes\Helpers;

/**
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
class Sort
{
    /**
     * @param array $order
     */
    public function __construct(array $order)
    {
        $this->order = $order;
    }

    /**
     * @param array $data
     * @return array
     */
    public function run(array $data)
    {
        usort($data, [$this, 'compare']);
        return $data;
    }

    /**
     * @param string $a
     * @param string $b
     * @return int
     */
    private function compare($a, $b)
    {
        foreach ($this->order as $k => $asc) {
            if ($a[$k] === $b[$k]) {
                continue;
            }
            $r = ($a[$k] > $b[$k]) ? 1 : -1;
            if (!$asc) {
                $r = 0 - $r;
            }
            return $r;
        }
        return 0;
    }

    /**
     * @var array
     */
    private $order;
}
