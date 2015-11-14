<?php
/**
 * @copyright   2015 Tom Stapersma, Caelaris
 * @license     MIT
 * @author      Tom Stapersma (info@caelaris.com)
 */
namespace DI\Tests\Dummy;

class DummyClassJ
{
    public $classA1;
    public $classA2;

    public function __construct(DummyClassA $classA1, DummyClassA $classA2)
    {
        $this->classA1 = $classA1;
        $this->classA2 = $classA2;
    }
}