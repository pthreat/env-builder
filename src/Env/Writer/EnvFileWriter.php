<?php declare(strict_types=1);

namespace LDL\Env\Writer;

use LDL\Env\Config\EnvConfig;

class EnvFileWriter implements EnvFileWriterInterface
{
    /**
     * @var Options\EnvWriterOptions
     */
    private $options;

    public function __construct(Options\EnvWriterOptions $options = null)
    {
        $this->options = $options ?? Options\EnvWriterOptions::fromArray([]);
    }

    /**
     * {@inheritdoc}
     */
    public function write(EnvConfig $config, string $content): void
    {
        $options = $this->options;

        if(false === $options->isForce() && true === file_exists($options->getFilename())){
            $msg = sprintf(
                'File: %s already exists!. Force it to overwrite',
                $options->getFilename()
            );

            throw new Exception\FileAlreadyExistsException($msg);
        }

        file_put_contents(
            $config->getGeneratedAs(),
            json_encode($config->toArray(), JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );

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