<?php
/**
 * @copyright   2015 Tom Stapersma, Caelaris
 * @license     MIT
 * @author      Tom Stapersma (info@caelaris.com)
 */

namespace DI\Tests;

class JsonLoaderTest extends \PHPUnit_Framework_TestCase
{
    public $className = 'DI\JsonLoader';

    protected function getDummyConfigFilePath($file)
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
        $methodExists = method_exists($this->className, 'setDiFilePath');
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
        $this->assertTrue($reflector->implementsInterface('DI\LoaderInterface'));
    }

    public function testImplementsFileLoaderInterface()
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

        $jsonLoader->setDiFilePath($this->getDummyConfigFilePath('non-existent-file.json'));
    }

    public function testSetDiFileMethodShouldThrowExceptionIfFileIsNotJson()
    {
        $this->setExpectedException('\InvalidArgumentException', 'DI File is not a .json file: ');

        /** @var \DI\JsonLoader $jsonLoader */
        $jsonLoader = new $this->className;

        $jsonLoader->setDiFilePath($this->getDummyConfigFilePath('wrong_extension.txt'));
    }

    public function testSetDiFileMethodShouldThrowExceptionIfFileContentIsNotJson()
    {
        $this->setExpectedException('\InvalidArgumentException', 'DI File content is not valid JSON: ');

        /** @var \DI\JsonLoader $jsonLoader */
        $jsonLoader = new $this->className;

        $jsonLoader->setDiFilePath($this->getDummyConfigFilePath('not_valid.json'));
    }

    public function testSetDiFileMethodShouldSetDiFileProperty()
    {
        /** @var \DI\JsonLoader $jsonLoader */
        $jsonLoader = new $this->className;

        $path = $this->getDummyConfigFilePath('valid.json');
        $jsonLoader->setDiFilePath($path);
        $this->assertEquals($path, $jsonLoader->diFilePath);
    }

    public function testConstructorMethodShouldNotSetDiFilePropertyIfNotPassedToConstructor()
    {
        /** @var \DI\JsonLoader $jsonLoader */
        $jsonLoader = new $this->className;

        $this->assertNull($jsonLoader->diFilePath);
    }

    public function testConstructorMethodShouldSetDiFilePropertyIfPassedToConstructor()
    {
        $path = $this->getDummyConfigFilePath('valid.json');

        /** @var \DI\JsonLoader $jsonLoader */
        $jsonLoader = new $this->className($path);

        $this->assertEquals($path, $jsonLoader->diFilePath);
    }

    public function testGetDiRepositoryMethodShouldReturnDiArray()
    {
        $expected = array(
            'DI\Tests\Dummy\DummyClassA' => array(
                'class' => 'DI\Tests\Dummy\DummyClassA'
            )
        );

        $path = $this->getDummyConfigFilePath('valid.json');

        /** @var \DI\JsonLoader $jsonLoader */
        $jsonLoader = new $this->className($path);

        $diRepository = $jsonLoader->getDiRepository();

        $this->assertEquals($expected, $diRepository);
    }

    public function testGetDiRepositoryMethodShouldThrowExceptionIfNoDiFileIsSet()
    {
        $this->setExpectedException('\Exception', 'No DI file has been set');

        /** @var \DI\JsonLoader $jsonLoader */
        $jsonLoader = new $this->className;

        $jsonLoader->getDiRepository();
    }

    public function testGetDiRepositoryShouldNotParseNewDiFileIfAlreadyParsedOldOne()
    {
        $expected = array(
            'DI\Tests\Dummy\DummyClassA' => array(
                'class' => 'DI\Tests\Dummy\DummyClassA'
            )
        );

        $path = $this->getDummyConfigFilePath('valid.json');
        $path2 = $this->getDummyConfigFilePath('valid2.json');

        /** @var \DI\JsonLoader $jsonLoader */
        $jsonLoader = new $this->className($path);

        $jsonLoader->getDiRepository();

        $jsonLoader->diFilePath = $path2;

        $diRepository = $jsonLoader->getDiRepository();

        $this->assertEquals($expected, $diRepository);
    }

    public function testGetDiRepositoryShouldParseNewDiFileIfSetThroughSetDiFileMethod()
    {
        $expected = array(
            'DI\Tests\Dummy\DummyClassB' => array(
                'class' => 'DI\Tests\Dummy\DummyClassB'
            )
        );

        $path = $this->getDummyConfigFilePath('valid.json');
        $path2 = $this->getDummyConfigFilePath('valid2.json');

        /** @var \DI\JsonLoader $jsonLoader */
        $jsonLoader = new $this->className($path);

        $jsonLoader->getDiRepository();

        $jsonLoader->setDiFilePath($path2);

        $diRepository = $jsonLoader->getDiRepository();

        $this->assertEquals($expected, $diRepository);
    }
}