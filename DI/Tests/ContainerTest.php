<?php
/**
 * @copyright   2015 Tom Stapersma, Caelaris
 * @license     MIT
 * @author      Tom Stapersma (info@caelaris.com)
 */
namespace DI\Tests;

use DI\JsonLoader;
use DI\Tests\Dummy\DummyClassA;

/**
 * Class ContainerTest
 *
 * @package DI\Tests
 */
class ContainerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * DI container class
     * @var string
     */
    public $className = 'DI\Container';

    protected function getDummyConfigFilePath($file)
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'DummyConfig' . DIRECTORY_SEPARATOR . $file;
    }

    public function testDiContainerExists()
    {
        $classExists = class_exists($this->className);
        $this->assertTrue($classExists);
    }

    public function testRegisterMethodExists()
    {
        $methodExists = method_exists($this->className, 'register');
        $this->assertTrue($methodExists);
    }

    public function testBuildMethodExists()
    {
        $methodExists = method_exists($this->className, 'build');
        $this->assertTrue($methodExists);
    }

    public function testGetClassConstructorParametersMethodExists()
    {
        $methodExists = method_exists($this->className, 'getClassConstructorParameters');
        $this->assertTrue($methodExists);
    }

    public function testRegisterMethodThrowsInvalidArgumentExceptionOnEmptyArray()
    {
        $this->setExpectedException('\InvalidArgumentException', 'Cannot register empty class');

        /** @var \DI\Container $diContainer */
        $diContainer = new $this->className;
        $diContainer->register(false, array());
    }

    public function testRegisterMethodRegistersClass()
    {
        /** @var \DI\Container $diContainer */
        $diContainer = new $this->className;
        $diContainer->register('\Test\Class', array(
            'class' => 'stdClass'
        ));

        $this->assertEquals($diContainer->repository['\Test\Class']['class'], 'stdClass');
    }

    public function testBuildMethodReturnsDummyClassA()
    {
        $testClassName = 'Test\Class';
        $resultClass = 'DI\Tests\Dummy\DummyClassA';

        /** @var \DI\Container $diContainer */
        $diContainer = new $this->className;
        $diContainer->register($testClassName, array('class' => $resultClass));

        $testClass = $diContainer->build($testClassName);

        $this->assertInstanceOf($resultClass, $testClass);
    }

    public function testBuildMethodReturnsDummyClassBWithDummyClassA()
    {
        $dummyClassA = 'DI\Tests\Dummy\DummyClassA';
        $dummyClassB = 'DI\Tests\Dummy\DummyClassB';

        /** @var \DI\Container $diContainer */
        $diContainer = new $this->className;
        $diContainer->register($dummyClassB, array('class' => $dummyClassB));

        $diContainer->register($dummyClassA, array('class' => $dummyClassA));

        /** @var \DI\Tests\Dummy\DummyClassB $testClass */
        $testClass = $diContainer->build($dummyClassB);

        $this->assertInstanceOf($dummyClassB, $testClass);

        $this->assertInstanceOf($dummyClassA, $testClass->classA);
    }

    public function testGetClassConstructorParametersReturnsDummyClassA()
    {
        $dummyClassA = 'DI\Tests\Dummy\DummyClassA';
        $dummyClassB = 'DI\Tests\Dummy\DummyClassB';

        /** @var \DI\Container $diContainer */
        $diContainer = new $this->className;
        $diContainer->register($dummyClassB, array('class' => $dummyClassB));

        $diContainer->register($dummyClassA, array('class' => $dummyClassA));

        $dummyClassAInstance = new DummyClassA;
        $expectedArgs = array($dummyClassAInstance);
        $dummyClassB = 'DI\Tests\Dummy\DummyClassB';

        $args = $diContainer->getClassConstructorParameters($dummyClassB);

        $this->assertEquals($expectedArgs, $args);
    }

    public function testGetClassConstructorParametersShouldReturnEmptyArrayIfClassHasNoConstructor()
    {
        $dummyClassA = 'DI\Tests\Dummy\DummyClassA';

        /** @var \DI\Container $diContainer */
        $diContainer = new $this->className;
        $diContainer->register($dummyClassA, array('class' => $dummyClassA));

        $args = $diContainer->getClassConstructorParameters($dummyClassA);

        $this->assertEmpty($args);
    }

    public function testGetClassConstructorParametersShouldReturnDefaultValueForParam()
    {
        $dummyClassC = 'DI\Tests\Dummy\DummyClassC';
        $defaultValue = array('defaultValue');

        /** @var \DI\Container $diContainer */
        $diContainer = new $this->className;
        $diContainer->register($dummyClassC, array('class' => $dummyClassC));

        /** @var \DI\Tests\Dummy\DummyClassC $testClass */
        $arg = $diContainer->getClassConstructorParameters($dummyClassC);

        $this->assertEquals($defaultValue, $arg);
    }

    public function testGetClassConstructorParametersShouldReturnDIValueForParam()
    {
        $dummyClassC = 'DI\Tests\Dummy\DummyClassC';
        $diValue = 'diValue';

        /** @var \DI\Container $diContainer */
        $diContainer = new $this->className;
        $diContainer->register(
            $dummyClassC,
            array(
                'class' => $dummyClassC,
                'param' => $diValue
            )
        );

        /** @var \DI\Tests\Dummy\DummyClassC $testClass */
        $arg = $diContainer->getClassConstructorParameters($dummyClassC);

        $this->assertEquals(array($diValue), $arg);
    }

    public function testGetClassConstructorParametersExceptionWhenNoValueSet()
    {
        $this->setExpectedException('\Exception', 'Cannot get required __construct value for');
        $dummyClassD = 'DI\Tests\Dummy\DummyClassD';

        /** @var \DI\Container $diContainer */
        $diContainer = new $this->className;
        $diContainer->register(
            $dummyClassD,
            array(
                'class' => $dummyClassD,
            )
        );

        /** @var \DI\Tests\Dummy\DummyClassD $testClass */
        $diContainer->getClassConstructorParameters($dummyClassD);
    }

    public function testBuildMethodShouldThrowExceptionForCircularReference()
    {
        $this->markTestSkipped('@todo: Implement Circular Reference Detection');

        $this->setExpectedException('\Exception', 'CircularReference error with class: ');
        $dummyClassE = 'DI\Tests\Dummy\DummyClassE';
        $dummyClassF = 'DI\Tests\Dummy\DummyClassF';

        /** @var \DI\Container $diContainer */
        $diContainer = new $this->className;

        $diContainer->register(
            $dummyClassE,
            array(
                'class' => $dummyClassE,
            )
        );

        $diContainer->register(
            $dummyClassF,
            array(
                'class' => $dummyClassF,
            )
        );

        $diContainer->build($dummyClassE);
    }

    public function testBuildMethodInterfaceShouldReturnConcreteClass()
    {
        $dummyClassA = 'DI\Tests\Dummy\DummyClassA';
        $dummyClassB = 'DI\Tests\Dummy\DummyClassB';
        $dummyClassG = 'DI\Tests\Dummy\DummyClassG';
        $dummyClassGInterface = 'DI\Tests\Dummy\DummyInterface';
        $dummyClassH = 'DI\Tests\Dummy\DummyClassH';

        /** @var \DI\Container $diContainer */
        $diContainer = new $this->className;

        $diContainer->register($dummyClassA, array('class' => $dummyClassA));
        $diContainer->register($dummyClassB, array('class' => $dummyClassB));
        $diContainer->register($dummyClassGInterface, array('class' => $dummyClassG));
        $diContainer->register($dummyClassH, array('class' => $dummyClassH));

        /** @var \DI\Tests\Dummy\DummyClassH $builtClassH */
        $builtClassH = $diContainer->build($dummyClassH);
        $this->assertInstanceOf($dummyClassH, $builtClassH);
        $this->assertInstanceOf($dummyClassG, $builtClassH->classG);
        $this->assertInstanceOf($dummyClassB, $builtClassH->classG->classB);
        $this->assertInstanceOf($dummyClassA, $builtClassH->classG->classB->classA);

        $this->assertEquals('test', $builtClassH->classG->returnTest());
    }

    public function testBuildStackShouldBeEmptyAfterBuild()
    {
        $this->markTestSkipped('@todo: Implement Circular Reference Detection');
        $dummyClassA = 'DI\Tests\Dummy\DummyClassA';

        /** @var \DI\Container $diContainer */
        $diContainer = new $this->className;
        $diContainer->register($dummyClassA, array('class' => $dummyClassA));
        $diContainer->build($dummyClassA);

        $this->assertEmpty($diContainer->buildStack);
    }

    public function testBuildMethodSequentialRegistration()
    {
        $dummyClassA = 'DI\Tests\Dummy\DummyClassA';
        $dummyClassB = 'DI\Tests\Dummy\DummyClassB';
        $dummyClassC = 'DI\Tests\Dummy\DummyClassC';

        /** @var \DI\Container $diContainer */
        $diContainer = new $this->className;

        $diContainer->register($dummyClassA, array('class' => $dummyClassC));
        $diContainer->register($dummyClassB, array('class' => $dummyClassA));
        $diContainer->register($dummyClassC, array('class' => $dummyClassC));

        $builtClass = $diContainer->build($dummyClassB);

        $this->assertInstanceOf($dummyClassC, $builtClass);
    }

    public function testBuildMethodSequentialRegistrationShouldThrowException()
    {
        $this->markTestSkipped('@todo: Implement Circular Reference Detection');
        $this->setExpectedException('\Exception', 'CircularReference error with class: ');
        $dummyClassA = 'DI\Tests\Dummy\DummyClassA';
        $dummyClassB = 'DI\Tests\Dummy\DummyClassB';
        $dummyClassC = 'DI\Tests\Dummy\DummyClassC';
        $dummyClassI = 'DI\Tests\Dummy\DummyClassI';

        /** @var \DI\Container $diContainer */
        $diContainer = new $this->className;

        $diContainer->register($dummyClassA, array('class' => $dummyClassC));
        $diContainer->register($dummyClassB, array('class' => $dummyClassA));

        $builtClass = $diContainer->build($dummyClassI);

        $this->assertInstanceOf($dummyClassC, $builtClass);
    }

    public function testBuildMethodTwoSameParamClassesShouldBeAllowed()
    {
        $dummyClassA = 'DI\Tests\Dummy\DummyClassA';
        $dummyClassJ = 'DI\Tests\Dummy\DummyClassJ';

        /** @var \DI\Container $diContainer */
        $diContainer = new $this->className;
        $builtClass = $diContainer->build($dummyClassJ);

        $this->assertInstanceOf($dummyClassJ, $builtClass);
        $this->assertInstanceOf($dummyClassA, $builtClass->classA1);
        $this->assertInstanceOf($dummyClassA, $builtClass->classA2);
    }

    public function testBuildMethodShouldTryToLoadIfNoDI()
    {
        $dummyClassA = 'DI\Tests\Dummy\DummyClassA';

        /** @var \DI\Container $diContainer */
        $diContainer = new $this->className;

        $builtClass = $diContainer->build($dummyClassA);
        $this->assertInstanceOf($dummyClassA, $builtClass);
    }

    public function testContainerShouldHaveConstructor()
    {
        $reflector = new \ReflectionClass($this->className);
        $this->assertNotNull($reflector->getConstructor());
    }

    public function testContainerConstructorShouldTakeOneOptionalParameterForDiFile()
    {
        $reflector = new \ReflectionClass($this->className);
        $constructor = $reflector->getConstructor();

        $constructorParams = $constructor->getParameters();
        $this->assertEquals(1, count($constructorParams));

        /** @var \ReflectionParameter $constructorParam */
        $constructorParam = current($constructorParams);

        $this->assertTrue($constructorParam->isOptional());

        $this->assertEquals('DI\LoaderInterface', $constructorParam->getClass()->getName());
    }

    public function testBuildMethodShouldReturnTheCorrectClassAfterConstructingContainerWithLoaderParam()
    {
        $expectedRepository = array(
            'DI\Tests\Dummy\DummyClassB' => array(
                'class' => 'DI\Tests\Dummy\DummyClassA'
            )
        );

        $path = $this->getDummyConfigFilePath('valid3.json');
        $jsonLoader = new JsonLoader($path);

        /** @var \DI\Container $diContainer */
        $diContainer = new $this->className($jsonLoader);

        $this->assertEquals($expectedRepository, $diContainer->repository);

        $builtClass = $diContainer->build('DI\Tests\Dummy\DummyClassB');

        $this->assertInstanceOf('DI\Tests\Dummy\DummyClassA', $builtClass);
    }

    public function testRegisterMethodShouldAllowInstancesToBeRegisteredOnTheFly()
    {
        $dummyClassA = 'DI\Tests\Dummy\DummyClassA';
        $dummyClassB = 'DI\Tests\Dummy\DummyClassB';

        /** @var \DI\Container $diContainer */
        $diContainer = new $this->className;
        $diContainer->register($dummyClassB, array('instance' => new $dummyClassA));

        $builtClass = $diContainer->build($dummyClassB);

        $this->assertInstanceOf($dummyClassA, $builtClass);
    }

    public function testBuildMethodShouldThrowExceptionIfClassIsNotInstantiable()
    {
        $this->setExpectedException('\Exception', 'Class is not instantiable: ');
        $dummyClassA = 'DI\Tests\Dummy\DummyClassA';
        $dummyInterface = 'DI\Tests\Dummy\DummyInterface';

        /** @var \DI\Container $diContainer */
        $diContainer = new $this->className;
        $diContainer->register($dummyClassA, array('class' => $dummyInterface));

        $diContainer->build($dummyClassA);
    }

    public function testBuildMethodShouldReturnDiContainerInstance()
    {
        /** @var \DI\Container $diContainer */
        $diContainer = new $this->className;

        $builtContainer = $diContainer->build($this->className);

        $this->assertSame($diContainer, $builtContainer);
    }
}