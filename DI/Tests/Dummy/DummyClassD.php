<?php
/**
 * @copyright   2015 Tom Stapersma, Caelaris
 * @license     MIT
 * @author      Tom Stapersma (info@caelaris.com)
 */
namespace DI\Tests\Dummy;

class DummyClassD
{
    public $paramA;
    public function __construct($param)
    {
        $this->paramA = $param;
    }
}