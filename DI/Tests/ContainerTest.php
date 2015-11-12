<?php
/**
 * @copyright   2015 Tom Stapersma, Caelaris
 * @license     MIT
 * @author      Tom Stapersma (info@caelaris.com)
 */
namespace Di\Tests;

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

    public function testBuildMethodExists()
    {
        $methodExists = method_exists($this->className, 'build');
        $this->assertTrue($methodExists);
    }
}