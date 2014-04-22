<?php
/**
 * @package go\DB
 */

namespace go\DB;

/**
 * The autoloader for the library
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
final class Autoloader
{
    /**
     * Registers an autoloader for the library
     */
    public static function register()
    {
        if (!self::$autoloader) {
            self::$autoloader = new self(__NAMESPACE__, __DIR__);
            \spl_autoload_register(self::$autoloader);
        }
    }

    /**
     * Registers an autoloader for the unit tests of this library
     *
     * @param string $ns
     *        the namespace of the tests
     * @param string $dir
     *        the root directory of the test classes
     */
    public static function registerForTests($ns, $dir)
    {
        if (!self::$autoloaderForTests) {
            self::$autoloaderForTests = new self($ns, $dir);
            \spl_autoload_register(self::$autoloaderForTests);
        }
    }

    /**
     * The constructor
     *
     * @param string $namespace
     *        the root namespace of the classess
     * @param string $dir
     *        the root directory of the classess
     */
    private function __construct($namespace, $dir)
    {
        $this->namespace = $namespace;
        $this->dir = $dir;
    }

    /**
     * Invoke: loads a class by its name
     *
     * @param string $classname
     */
    public function __invoke($classname)
    {
        $prefix = $this->namespace.'\\';
        if (\strpos($classname, $prefix) !== 0) {
            return;
        }
        $short = \substr($classname, \strlen($prefix));
        $filename = \str_replace('\\', \DIRECTORY_SEPARATOR, $short);
        $filename = $this->dir.\DIRECTORY_SEPARATOR.$filename.'.php';
        if (\is_file($filename)) {
            require_once($filename);
        }
    }

    /**
     * @var string
     */
    private $namespace;

    /**
     * @var string
     */
    private $dir;

    /**
     * @var callable
     */
    private static $autoloader;

    /**
     * @var callable
     */
    private static $autoloaderForTests;
}
