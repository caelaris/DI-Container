<?php
/**
 * @copyright   2015 Tom Stapersma, Caelaris
 * @license     MIT
 * @author      Tom Stapersma (info@caelaris.com)
 */
namespace DI;

/**
 * Class JsonLoader
 *
 * @package DI
 */
class JsonLoader implements FileLoaderInterface,LoaderInterface
{
    const JSON_FILE_EXTENSION = 'json';
    public $diFilePath;
    protected $diRepository;

    /**
     * @param bool $diFile
     */
    public function __construct($diFile = false)
    {
        if ($diFile) {
            $this->setDiFilePath($diFile);
        }
    }

    /**
     * Validates and sets DI file path
     *
     * @param $diFile
     *
     * @return $this
     */
    public function setDiFilePath($diFile)
    {
        if (!file_exists($diFile)) {
            throw new \InvalidArgumentException('DI File does not exist: ' . $diFile);
        }

        if (pathinfo($diFile, PATHINFO_EXTENSION) != $this::JSON_FILE_EXTENSION) {
            throw new \InvalidArgumentException('DI File is not a .json file: ' . $diFile);
        }

        $content = json_decode(file_get_contents($diFile), true);
        if (!$content) {
            throw new \InvalidArgumentException('DI File content is not valid JSON: ' . $diFile);
        }

        $this->diFilePath = $diFile;
        $this->diRepository = null;

        return $this;
    }

    /**
     * Return the formatted array DI Repository with all registered DI overwrites
     *
     * @return array
     * @throws \Exception
     */
    public function getDiRepository()
    {
        if (!$this->diRepository) {
            if (!$this->diFilePath) {
                throw new \Exception('No DI file has been set');
            }

            $this->diRepository = json_decode(file_get_contents($this->diFilePath), true);
        }

        return $this->diRepository;
    }
}