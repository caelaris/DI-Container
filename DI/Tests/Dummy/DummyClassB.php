<?php
/**
 * @copyright   2015 Tom Stapersma, Caelaris
 * @license     MIT
 * @author      Tom Stapersma (info@caelaris.com)
 */
namespace DI\Tests\Dummy;

class DummyClassB
{
    public $classA;
    public function __construct(DummyClassA $classA)
    {
        $this->classA = $classA;
    }
}