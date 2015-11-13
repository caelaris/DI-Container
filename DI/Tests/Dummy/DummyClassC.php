<?php
/**
 * @copyright   2015 Tom Stapersma, Caelaris
 * @license     MIT
 * @author      Tom Stapersma (info@caelaris.com)
 */
namespace DI\Tests\Dummy;

class DummyClassC
{
    public $paramA;
    public function __construct($param = 'defaultValue')
    {
        $this->paramA = $param;
    }
}