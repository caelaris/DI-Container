<?php
/**
 * @copyright   2015 Tom Stapersma, Caelaris
 * @license     MIT
 * @author      Tom Stapersma (info@caelaris.com)
 */
namespace Di\Tests;

use DI\Container;
use DI\Tests\Dummy\DummyClassA;

/**
 * Class ContainerTest
 *
 * @package Di\Tests
 */
class ContainerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * DI container class
     * @var string
     */
    public $className = 'DI\Container';

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
        $this->markTestSkipped('@todo: Implement circular Reference Detection');

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
        $this->markTestSkipped('@todo: Implement circular Reference Detection');
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
        $this->markTestSkipped('@todo: Implement circular Reference Detection');
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
}