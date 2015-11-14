<?php
/**
 * @copyright   2015 Tom Stapersma, Caelaris
 * @license     MIT
 * @author      Tom Stapersma (info@caelaris.com)
 */
namespace DI\Tests\Dummy;

class DummyClassG implements DummyInterface
{
    public $classB;

    public function __construct(DummyClassB $classB)
    {
        $this->classB = $classB;
    }

    public function returnTest()
    {
        return 'test';
    }
}