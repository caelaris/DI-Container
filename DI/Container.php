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
    /**
     * Container for all DI exceptions
     *
     * @var array
     */
    public $repository = array();

    /**
     * @todo fix Circular Reference detection
     */
//    public $buildStack = array();

    /**
     * @param FileLoaderInterface $diFile
     */
    public function __construct(FileLoaderInterface $diFile = null)
    {

    }

    /**
     * Registers
     *
     * @param $class
     * @param $di
     */
    public function register($class, $di)
    {
        if (empty($class) || empty($di)) {
            throw new \InvalidArgumentException('Cannot register empty class');
        }

        $this->repository[$class] = $di;
    }

    /**
     * Build a class using DI
     *
     * @todo fix Circular Reference detection
     *
     * @param      $className
     * @param bool $resetBuildStack
     *
     * @return object
     * @throws \Exception
     */
    public function build($className, $resetBuildStack = true)
    {
        if (isset($this->repository[$className]['class'])) {
            /** If there is new class registered for this class, use that to instantiate */
            $buildClass = $this->repository[$className]['class'];
        } else {
            /** Else instantiate the className passed */
            $buildClass = $className;
        }
        /**
         * @todo fix Circular Reference detection
         */
//        if (!empty($this->buildStack) && in_array($buildClass, $this->buildStack)) {
//            /** If the class being loaded is already in the build stack, error out due to circular references */
//            throw new \Exception('CircularReference error with class: ' . $buildClass);
//        }
//
//        /** Add the current class to the build stack */
//        $this->buildStack[] = $buildClass;

        if (isset($this->repository[$buildClass]['class']) && $className != $buildClass) {
            /** If there is another class registered for the current class, follow the reference */
            return $this->build($this->repository[$buildClass]['class']);
        }

        /** Get the parameters required for the class constructor */
        $params = $this->getClassConstructorParameters($buildClass);

        /** Create a new instance of the class with the generated parameters */
        $reflector = new \ReflectionClass($buildClass);
        $newInstance = $reflector->newInstanceArgs($params);

        /**
         * @todo fix Circular Reference detection
         */
//        if ($resetBuildStack) {
//            $this->buildStack = array();
//        }

        return $newInstance;
    }

    /**
     * Parse a Class constructor and instantiate and returns parameters
     *
     * @param $className
     *
     * @return array
     * @throws \Exception
     */
    public function getClassConstructorParameters($className)
    {
        $instanceParameters = array();
        $reflector = new \ReflectionClass($className);
        $constructor = $reflector->getConstructor();
        if (!$constructor) {
            /** If class has no constructor, no arguments needed */
            return $instanceParameters;
        }

        $parameters = $constructor->getParameters();
        foreach ($parameters as $parameter) {
            if ($parameter->getClass()) {
                /** If the parameter is a class, build that class */
                $parameterClassName = $parameter->getClass()->getName();
                $param = $this->build($parameterClassName, false);
            } elseif(isset($this->repository[$className][$parameter->getName()])) {
                /** If there is a value for the parameter in the repository, use that value */
                $param = $this->repository[$className][$parameter->getName()];
            } elseif($parameter->isOptional() && $parameter->getDefaultValue()) {
                /** If the parameter has a default value, use that value */
                $param = $parameter->getDefaultValue();
            } else {
                /** If non of the options above have led to a parameter being set, we cannot instantiate this class */
                throw new \Exception('Cannot get required __construct value for: ' . $parameter->getName() . ' in class ' . $className);
            }
            $instanceParameters[] = $param;
        }
        return $instanceParameters;
    }
}