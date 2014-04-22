<?php
/**
 * Autoload for the library
 *
 * @deprecated since version 2.1.0
 */

namespace go\DB;

/**
 * Loads a class by its name
 *
 * @param string $classname
 *        the class name
 * @return bool
 *         TRUE if the class has been loaded
 */
function autoload($classname)
{
    if (\strpos($classname, __NAMESPACE__) !== 0) {
        return false;
    }
    $localname = \substr($classname, \strlen(__NAMESPACE__));
    $filename = __DIR__.\str_replace('\\', '/', $localname.'.php');
    if (!\file_exists($filename)) {
        return false;
    }
    require_once($filename);
    return \class_exists($classname, false);
}

/**
 * Registers an autoloader for the library
 */
function autoloadRegister()
{
    \spl_autoload_register('\go\DB\autoload');
}
