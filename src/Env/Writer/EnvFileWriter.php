<?php

namespace LDL\Env\Writer;

class EnvFileWriter implements EnvFileWriterInterface
{
    private $options;

    public function __construct(Options\EnvWriterOptions $options)
    {
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function write(string $content): void
    {
        $file = $this->options->getPath();

        if(true === file_exists($file) && false === $this->options->isForce()){
            $msg = sprintf(
                'File: %s in path: %s already exists!. Force it to overwrite',
                $this->options->getFilename(),
                $this->options->getDirectory()
            );

            throw new Exception\FileAlreadyExistsException($msg);
        }

        file_put_contents($file, $content);
    }
}