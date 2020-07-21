<?php

namespace LDL\Env\Writer\Options;

class EnvWriterOptions
{
    /**
     * @var string
     */
    private $filename = 'env-compiled';

    /**
     * @var bool
     */
    private $force = false;

    public function __construct()
    {
    }

    public static function fromArray(array $options) : self
    {
        $instance = new static();
        $defaults = get_object_vars($instance);

        foreach($options as $opt=>$value){
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

        return $instance->setFilename($merge['filename'])
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

}