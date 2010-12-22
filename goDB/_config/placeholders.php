<?php
/**
 * Конфигурация шаблонизатора запросов
 *
 * @package    go\DB
 * @subpackage config
 * @author     Григорьев Олег aka vasa_c
 */

return array(

    /* Список плейсхолдеров (основная краткая форма) */
    'placeholders' => array(
        '', 'l', 's', 'v', 't', 'c', 'e', 'q', 'xc',
    ),

    /* Список длинных синонимов */
    'longs' => array(
        'string' => '',
        'scalar' => '',
        'list'   => 'l',
        'set'    => 's',
        'values' => 'v',
        'table'  => 't',
        'col'    => 'c',
        'escape' => 'e',
        'query'  => 'q',
        'cols'   => 'xc',
    ),

    /* Список модификаторов (основная краткая форма) */
    'modifers' => array(
        'n', 'i', 'f', 'b',
    ),

    /* Список длинных синонимов модификаторов */
    'longModifers' => array(
        'null'  => 'n',
        'int'   => 'i',
        'float' => 'f',
        'bool'  => 'b',
    ),
);