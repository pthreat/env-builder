<?php

namespace LDL\Env\Writer;

class EnvFileWriter implements EnvFileWriterInterface
{
    /**
     * {@inheritdoc}
     */
    public function write(string $content, Options\EnvWriterOptions $options=null): void
    {
        $options = $options ?? new Options\EnvWriterOptions();

        if(false === $options->isForce() && true === file_exists($options->getFilename())){
            $msg = sprintf(
                'File: %s already exists!. Force it to overwrite',
                $options->getFilename()
            );

            throw new Exception\FileAlreadyExistsException($msg);
        }

        file_put_contents($options->getFilename(), $content);
    }
}