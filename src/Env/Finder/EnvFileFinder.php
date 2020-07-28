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

    public function __construct(Options\EnvFileFinderOptions $options = null)
    {
        $this->options = $options ?? Options\EnvFileFinderOptions::fromArray([]);
    }

    /**
     * {@inheritdoc}
     */
    public function find() : GenericFileCollection
    {
        $options = $this->options;

        $files = LocalFileFinder::find($options->getDirectories(), $options->getFiles(), true);

        if(!count($files)){
            $msg = sprintf(
                'No files were found matching: "%s" in directories: "%s"',
                implode(', ', $options->getFiles()),
                implode(', ', $options->getDirectories())
            );

            throw new Exception\NoFilesFoundException($msg);
        }

        return $files;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions(): Options\EnvFileFinderOptions
    {
        return $this->options;
    }

}