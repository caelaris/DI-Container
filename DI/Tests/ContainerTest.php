<?php
/**
 * @copyright   2015 Tom Stapersma, Caelaris
 * @license     MIT
 * @author      Tom Stapersma (info@caelaris.com)
 */
namespace Di\Tests;

use DI\Container;
use DI\Tests\Dummy\DummyClassA;

class ContainerTest extends \PHPUnit_Framework_TestCase
{
    public $className = '\DI\Container';

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

    public function testRegisterMethodThrowsInvalidArgumentExceptionOnEmptyArray()
    {
        $this->setExpectedException('\InvalidArgumentException', 'Cannot register empty class');
        $diContainer = new Container;
        $diContainer->register(false, array());
    }

    public function testRegisterMethodRegistersClass()
    {
        $diContainer = new Container;
        $diContainer->register('\Test\Class', array(
            'class' => 'stdClass'
        ));

        $this->assertEquals($diContainer->repository['\Test\Class']['class'], 'stdClass');
    }

    public function testBuildMethodExists()
    {
        $methodExists = method_exists($this->className, 'build');
        $this->assertTrue($methodExists);
    }

    /**
     * @depends testBuildMethodExists
     */
    public function testBuildMethodReturnsDummyClassA()
    {
        $testClassName = 'Test\Class';
        $resultClass = 'DI\Tests\Dummy\DummyClassA';

        $diContainer = new Container;
        $diContainer->register($testClassName, array('class' => $resultClass));

        $testClass = $diContainer->build($testClassName);

        $this->assertInstanceOf($resultClass, $testClass);
    }

    public function testBuildMethodReturnsDummyClassBWithDummyClassA()
    {
        $dummyClassA = 'DI\Tests\Dummy\DummyClassA';
        $dummyClassB = 'DI\Tests\Dummy\DummyClassB';

        $diContainer = new Container;
        $diContainer->register($dummyClassB, array('class' => $dummyClassB));

        $diContainer->register($dummyClassA, array('class' => $dummyClassA));

        /** @var \DI\Tests\Dummy\DummyClassB $testClass */
        $testClass = $diContainer->build($dummyClassB);

        $this->assertInstanceOf($dummyClassB, $testClass);

        $this->assertInstanceOf($dummyClassA, $testClass->classA);
    }

    public function testGetClassConstructorArgsMethodExists()
    {
        $methodExists = method_exists($this->className, 'getClassConstructorArgs');
        $this->assertTrue($methodExists);
    }

    /**
     * @depends testGetClassConstructorArgsMethodExists
     */
    public function testGetClassConstructorArgsReturnsDummyClassA()
    {
        $dummyClassA = 'DI\Tests\Dummy\DummyClassA';
        $dummyClassB = 'DI\Tests\Dummy\DummyClassB';

        $diContainer = new Container;
        $diContainer->register($dummyClassB, array('class' => $dummyClassB));

        $diContainer->register($dummyClassA, array('class' => $dummyClassA));

        $dummyClassAInstance = new DummyClassA;
        $expectedArgs = array($dummyClassAInstance);
        $dummyClassB = 'DI\Tests\Dummy\DummyClassB';

        $args = $diContainer->getClassConstructorArgs($dummyClassB);

        $this->assertEquals($expectedArgs, $args);
    }

    public function testGetClassConstructorArgsShouldReturnEmptyArrayIfClassHasNoConstructor()
    {
        $dummyClassA = 'DI\Tests\Dummy\DummyClassA';
        $diContainer = new Container;
        $diContainer->register($dummyClassA, array('class' => $dummyClassA));

        $args = $diContainer->getClassConstructorArgs($dummyClassA);

        $this->assertEmpty($args);
    }

    public function testGetClassConstructorArgsShouldReturnDefaultValueForParam()
    {
        $dummyClassC = 'DI\Tests\Dummy\DummyClassC';
        $defaultValue = array('defaultValue');

        $diContainer = new Container;
        $diContainer->register($dummyClassC, array('class' => $dummyClassC));

        /** @var \DI\Tests\Dummy\DummyClassC $testClass */
        $arg = $diContainer->getClassConstructorArgs($dummyClassC);

        $this->assertEquals($defaultValue, $arg);
    }

    public function testGetClassConstructorArgsShouldReturnDIValueForParam()
    {
        $dummyClassC = 'DI\Tests\Dummy\DummyClassC';
        $diValue = 'diValue';

        $diContainer = new Container;
        $diContainer->register(
            $dummyClassC,
            array(
                'class' => $dummyClassC,
                'param' => $diValue
            )
        );

        /** @var \DI\Tests\Dummy\DummyClassC $testClass */
        $arg = $diContainer->getClassConstructorArgs($dummyClassC);

        $this->assertEquals(array($diValue), $arg);
    }

    public function testGetClassConstructorArgsExceptionWhenNoValueSet()
    {
        $this->setExpectedException('\Exception', 'Cannot get required __construct value for');
        $dummyClassD = 'DI\Tests\Dummy\DummyClassD';

        $diContainer = new Container;
        $diContainer->register(
            $dummyClassD,
            array(
                'class' => $dummyClassD,
            )
        );

        /** @var \DI\Tests\Dummy\DummyClassD $testClass */
        $diContainer->getClassConstructorArgs($dummyClassD);
    }
}