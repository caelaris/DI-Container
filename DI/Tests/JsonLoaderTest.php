<?php
/**
 * @copyright   2015 Tom Stapersma, Caelaris
 * @license     MIT
 * @author      Tom Stapersma (info@caelaris.com)
 */

namespace DI\Tests;

class JsonLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * DI container class
     * @var string
     */
    public $className = 'DI\JsonLoader';

    protected function getConfigFilePath($file)
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'DummyConfig' . DIRECTORY_SEPARATOR . $file;
    }

    public function testJsonLoaderExists()
    {
        $classExists = class_exists($this->className);
        $this->assertTrue($classExists);
    }

    public function testSetDiFileMethodExists()
    {
        $methodExists = method_exists($this->className, 'setDiFile');
        $this->assertTrue($methodExists);
    }

    public function testGetDiRepositoryMethodExists()
    {
        $methodExists = method_exists($this->className, 'getDiRepository');
        $this->assertTrue($methodExists);
    }

    public function testImplementsLoaderInterface()
    {
        $reflector = new \ReflectionClass($this->className);
        $this->assertTrue($reflector->implementsInterface('DI\FileLoaderInterface'));
    }

    public function testConstructorShouldTakeOptionalStringParameter()
    {
        $reflector = new \ReflectionClass($this->className);
        $constructor = $reflector->getConstructor();

        $this->assertNotNull($constructor);

        $constructorParams = $constructor->getParameters();
        $this->assertEquals(1, count($constructorParams));

        /** @var \ReflectionParameter $constructorParam */
        $constructorParam = current($constructorParams);

        $this->assertTrue($constructorParam->isOptional());

        $this->assertNull($constructorParam->getClass());
    }

    public function testSetDiFileMethodShouldThrowExceptionIfFileDoesNotExist()
    {
        $this->setExpectedException('\InvalidArgumentException', 'DI File does not exist: ');

        /** @var \DI\JsonLoader $jsonLoader */
        $jsonLoader = new $this->className;

        $jsonLoader->setDiFile($this->getConfigFilePath('non-existent-file.json'));
    }

    public function testSetDiFileMethodShouldThrowExceptionIfFileIsNotJson()
    {
        $this->setExpectedException('\InvalidArgumentException', 'DI File is not a .json file: ');

        /** @var \DI\JsonLoader $jsonLoader */
        $jsonLoader = new $this->className;

        $jsonLoader->setDiFile($this->getConfigFilePath('wrong_extension.txt'));
    }
}