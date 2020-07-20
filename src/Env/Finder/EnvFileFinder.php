<?php

namespace LDL\Env\Finder;

use LDL\FS\Finder\Adapter\LocalFileFinder;
use LDL\FS\Type\Types\Generic\Collection\GenericFileCollection;

class EnvFileFinder implements EnvFileFinderInterface
{
    /**
     * @var Options\EnvFileFinderOptions
     */
    private $options;

    public function __construct(Options\EnvFileFinderOptions $options)
    {
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function find() : GenericFileCollection
    {
        $files = LocalFileFinder::find($this->options->getFiles(), $this->options->getDirectories());

        if(!count($files)){
            $msg = sprintf(
                'No ENV files were found matching: %s',
                implode(', ', $this->options->getFiles())
            );
            throw new Exception\NoFilesFoundException($msg);
        }

        return $files;
    }

}