<?php
/**
 * @copyright   2015 Tom Stapersma, Caelaris
 * @license     MIT
 * @author      Tom Stapersma (info@caelaris.com)
 */
namespace DI;

interface FileLoaderInterface
{
    public function setDiFile($diFile);
    public function getDiRepository();
}