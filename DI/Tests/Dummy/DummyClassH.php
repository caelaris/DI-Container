<?php
/**
 * @copyright   2015 Tom Stapersma, Caelaris
 * @license     MIT
 * @author      Tom Stapersma (info@caelaris.com)
 */
namespace DI\Tests\Dummy;

class DummyClassH
{
    public $classG;
    public function __construct(DummyInterface $classG)
    {
        $this->classG = $classG;
    }
}