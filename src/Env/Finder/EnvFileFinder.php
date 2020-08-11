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

    /**
     * @var GenericFileCollection
     */
    private $files;

    public function __construct(Options\EnvFileFinderOptions $options = null)
    {
        $this->options = $options ?? Options\EnvFileFinderOptions::fromArray([]);
        $this->files = new GenericFileCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function find(bool $cache = false) : GenericFileCollection
    {
        if(true === $cache){
            return $this->files;
        }

        $options = $this->options;

        $files = LocalFileFinder::find($options->getDirectories(), $options->getFiles(), true);

        $return = new GenericFileCollection();

        /**
         * @var GenericFileType $file
         */
        foreach($files as $key => $file){
            $unset = false;

            if(in_array($file->getRealPath(), $this->options->getExcludedFiles(), true)){
                $unset = true;
            }

            foreach($this->options->getExcludedDirectories() as $directory){
                $path = new UnicodeString($file->getPath());
                $dir = new UnicodeString($directory);

                if(true === $path->startsWith($dir)){
                    $unset = true;
                    break;
                }
            }

            if(true === $unset){
                continue;
            }

            $return->append($file);
        }

        if(!count($return)){
            $msg = sprintf(
                'No files were found matching: "%s" in directories: "%s"',
                implode(', ', $options->getFiles()),
                implode(', ', $options->getDirectories())
            );

            throw new Exception\NoFilesFoundException($msg);
        }

        $this->files = $return;
        return $return;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions(): Options\EnvFileFinderOptions
    {
        return $this->options;
    }

}