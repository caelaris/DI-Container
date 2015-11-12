<?php
/**
 * @copyright   2015 Tom Stapersma, Caelaris
 * @license     MIT
 * @author      Tom Stapersma (info@caelaris.com)
 */
namespace DI;

/**
 * Class Container
 *
 * @package DI
 */
class Container
{
    public $repository = array();

    public function register($class = array())
    {
        if (empty($class)) {
            throw new \InvalidArgumentException('Cannot register empty class');
        }

        $className = $class[0];
        $class = $class[1];

        $this->repository[$className] = $class;
    }

    public function build($className)
    {
        $buildClass = $this->repository[$className];
        return new $buildClass;
    }
}