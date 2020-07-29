<?php

declare(strict_types=1);

namespace LDL\Env\Writer;

class EnvFileWriter implements EnvFileWriterInterface
{
    private $options;

    public function __construct(Options\EnvWriterOptions $options = null)
    {
        $this->options = $options ?? Options\EnvWriterOptions::fromArray([]);
    }

    /**
     * {@inheritdoc}
     */
    public function write(string $content): void
    {
        $options = $this->options;

        if(false === $options->isForce() && true === file_exists($options->getFilename())){
            $msg = sprintf(
                'File: %s already exists!. Force it to overwrite',
                $options->getFilename()
            );

            throw new Exception\FileAlreadyExistsException($msg);
        }

        file_put_contents($options->getFilename(), $content);
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions(): Options\EnvWriterOptions
    {
        return clone($this->options);
    }
}