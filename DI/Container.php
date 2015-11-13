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

    public function register($class, $di)
    {
        if (empty($class) || empty($di)) {
            throw new \InvalidArgumentException('Cannot register empty class');
        }

        $this->repository[$class] = $di;
    }

    public function build($className)
    {
        $buildClass = $this->repository[$className]['class'];

        $args = $this->getClassConstructorArgs($buildClass);

        $reflector = new \ReflectionClass($buildClass);
        $newInstance = $reflector->newInstanceArgs($args);

        return $newInstance;
    }

    public function getClassConstructorArgs($className)
    {
        $constructorArgs = array();
        $reflector = new \ReflectionClass($className);
        $constructor = $reflector->getConstructor();
        if (!$constructor) {
            /** If class has no constructor, no arguments needed */
            return $constructorArgs;
        }

        $reflectionConstructorArgs = $constructor->getParameters();
        foreach ($reflectionConstructorArgs as $reflectionConstructorArg) {
            if ($reflectionConstructorArg->getClass()) {
                $reflectionConstructorArgClassName = $reflectionConstructorArg->getClass()->getName();
                $constructorArg = $this->build($reflectionConstructorArgClassName);
            } elseif(isset($this->repository[$className][$reflectionConstructorArg->getName()])) {
                $constructorArg = $this->repository[$className][$reflectionConstructorArg->getName()];
            } elseif($reflectionConstructorArg->isOptional() && $reflectionConstructorArg->getDefaultValue()) {
                $constructorArg = $reflectionConstructorArg->getDefaultValue();
            } else {
                throw new \Exception('Cannot get required __construct value for: ' . $reflectionConstructorArg->getName() . ' in class ' . $className);
            }
            $constructorArgs[] = $constructorArg;
        }
        return $constructorArgs;
    }
}