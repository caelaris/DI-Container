<?php
/**
 * @copyright   2015 Tom Stapersma, Caelaris
 * @license     MIT
 * @author      Tom Stapersma (info@caelaris.com)
 */
namespace DI;

class JsonLoader implements FileLoaderInterface
{
    const JSON_FILE_EXTENSION = '.json';
    protected $diFile;

    public function __construct($diFile = false)
    {

    }

    public function setDiFile($diFile)
    {
        if (!file_exists($diFile)) {
            throw new \InvalidArgumentException('DI File does not exist: ' . $diFile);
        }

        if (pathinfo($diFile, PATHINFO_EXTENSION)) {
            throw new \InvalidArgumentException('DI File is not a .json file: ' . $diFile);
        }
    }

    public function getDiRepository()
    {

    }
}