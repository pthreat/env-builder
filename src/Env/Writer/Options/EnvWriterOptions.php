<?php

namespace LDL\Env\Writer\Options;

class EnvWriterOptions
{
    /**
     * @var string
     */
    private $filename;

    /**
     * @var string
     */
    private $directory;

    /**
     * @var bool
     */
    private $force = false;

    public static function fromArray(array $options)
    {
        $instance = new static();
        $defaults = get_object_vars($instance);

        foreach($options as $opt){
            if(array_key_exists($opt, $defaults)) {
                continue;
            }
            $msg = sprintf(
                'Unknown option: "%s", valid options are: %s',
                $opt,
                implode(', ', array_keys($defaults))
            );

            throw new Exception\UnknownOptionException($msg);
        }

        $merge = array_merge($defaults, $options);

        if(null === $merge['directory']){
            $merge['directory'] = getcwd();
        }

        return $instance->setFilename($merge['filename'])
            ->setDirectory($merge['directory'])
            ->setForce($merge['force']);
    }

    /**
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * @param string $filename
     * @return EnvWriterOptions
     */
    private function setFilename(string $filename): EnvWriterOptions
    {
        $this->filename = $filename;
        return $this;
    }

    /**
     * @return string
     */
    public function getDirectory(): string
    {
        return $this->directory;
    }

    /**
     * @param string $directory
     * @return EnvWriterOptions
     */
    private function setDirectory(string $directory): EnvWriterOptions
    {
        $this->directory = $directory;
        return $this;
    }

    /**
     * @return bool
     */
    public function isForce(): bool
    {
        return $this->force;
    }

    /**
     * @param bool $force
     * @return EnvWriterOptions
     */
    private function setForce(bool $force): EnvWriterOptions
    {
        $this->force = $force;
        return $this;
    }

    public function getPath(): string
    {
        return sprintf(
            '%s%s%s',
            $this->getDirectory(),
            DIRECTORY_SEPARATOR,
            $this->getFilename()
        );
    }
}