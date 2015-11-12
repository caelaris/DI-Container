<?php
/**
 * @copyright   2015 Tom Stapersma, Caelaris
 * @license     MIT
 * @author      Tom Stapersma (info@caelaris.com)
 */
namespace Di\Tests;

use DI\Container;

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
        $diContainer->register(array());
    }

    public function testRegisterMethodRegistersClass()
    {
        $diContainer = new Container;
        $diContainer->register(
            array(
                '\Test\Class',
                'stdClass'
            )
        );

        $this->assertEquals($diContainer->repository['\Test\Class'], 'stdClass');
    }

    public function testBuildMethodExists()
    {
        $methodExists = method_exists($this->className, 'build');
        $this->assertTrue($methodExists);
    }

    public function testBuildMethodReturnsStdClass()
    {
        $diContainer = new Container;
        $diContainer->register(
            array(
                '\Test\Class',
                '\stdClass'
            )
        );

        $testClass = $diContainer->build('\Test\Class');

        $this->assertInstanceOf('\stdClass', $testClass);
    }
}