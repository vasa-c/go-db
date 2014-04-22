<?php
/**
 * The list of result formats
 *
 * a format name => TRUE, if it intended for SELECT
 *
 * @package go\DB
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */

return array(
    'assoc'     => true,
    'numerics'  => true,
    'objects'   => true,
    'col'       => true,
    'vars'      => true,
    'iassoc'    => true,
    'inumerics' => true,
    'iobjects'  => true,
    'icol'      => true,
    'ivars'     => true,
    'row'       => true,
    'numeric'   => true,
    'object'    => true,
    'el'        => true,
    'bool'      => true,
    'num'       => true,
    'id'        => false,
    'ar'        => false,
    'cursor'    => false,
);
