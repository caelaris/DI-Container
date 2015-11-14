<?php
/**
 * @copyright   2015 Tom Stapersma, Caelaris
 * @license     MIT
 * @author      Tom Stapersma (info@caelaris.com)
 */
namespace DI\Tests\Dummy;

class DummyClassF
{
    public $classE;
    public function __construct(DummyClassE $classE)
    {
        $this->classE = $classE;
    }
}