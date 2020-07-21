<?php

namespace LDL\Env\Finder;

use LDL\FS\Finder\Adapter\LocalFileFinder;
use LDL\FS\Type\Types\Generic\Collection\GenericFileCollection;

class EnvFileFinder implements EnvFileFinderInterface
{
    /**
     * {@inheritdoc}
     */
    public function find(Options\EnvFileFinderOptions $options=null) : GenericFileCollection
    {
        $options =  $options ?? Options\EnvFileFinderOptions::fromArray([]);

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

}