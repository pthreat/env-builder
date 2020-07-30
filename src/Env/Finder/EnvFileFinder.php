<?php

declare(strict_types=1);

namespace LDL\Env\Finder;

use LDL\FS\Finder\Adapter\LocalFileFinder;
use LDL\FS\Type\Types\Generic\Collection\GenericFileCollection;
use LDL\FS\Type\Types\Generic\GenericFileType;
use Symfony\Component\String\UnicodeString;

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

        /**
         * @var GenericFileType $file
         */
        foreach($files as $key => $file){
            if(in_array($file->getRealPath(), $this->options->getExcludedFiles(), true)){
                unset($files[$key]);
            }

            foreach($this->options->getExcludedDirectories() as $directory){
                $path = new UnicodeString($file->getPath());
                $dir = new UnicodeString($directory);

                if(true === $path->startsWith($dir)){
                    unset($files[$key]);
                }
            }
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