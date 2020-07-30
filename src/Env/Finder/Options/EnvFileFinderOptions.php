<?php

declare(strict_types=1);

namespace LDL\Env\Finder\Options;

use LDL\Env\Interfaces\OptionsInterface;

class EnvFileFinderOptions implements OptionsInterface
{
    /**
     * @var array
     */
    private $directories = [];

    /**
     * @var array
     */
    private $files = [
        '.env'
    ];

    /**
     * @var array
     */
    private $excludedDirectories = [];

    /**
     * @var array
     */
    private $excludedFiles = [];

    private function __construct()
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

        return $instance->setDirectories($merge['directories'])
            ->setFiles($merge['files'])
            ->setExcludedDirectories($merge['excludedDirectories'])
            ->setExcludedFiles($merge['excludedFiles']);
    }

    /**
     * @return array
     */
    public function toArray() : array
    {
        return get_object_vars($this);
    }

    /**
     * @return array
     */
    public function jsonSerialize() : array
    {
        return $this->toArray();
    }

    /**
     * @return array
     */
    public function getFiles(): array
    {
        return $this->files;
    }

    /**
     * @param array $files
     * @return EnvFileFinderOptions
     * @throws Exception\InvalidOptionException
     */
    private function setFiles(array $files): EnvFileFinderOptions
    {
        if(0 === count($files)){
            throw new Exception\InvalidOptionException('No files to find were given');
        }

        $this->files = $files;
        return $this;
    }

    /**
     * @return array
     */
    public function getDirectories(): array
    {
        return $this->directories;
    }

    /**
     * @param array $directories
     * @return EnvFileFinderOptions
     */
    private function setDirectories(array $directories): EnvFileFinderOptions
    {
        if(0 === count($directories)){
            $directories[] = \getcwd();
        }

        $this->directories = $directories;
        return $this;
    }

    /**
     * @return array
     */
    public function getExcludedDirectories(): array
    {
        return $this->excludedDirectories;
    }

    /**
     * @param array $excludedDirectories
     * @return EnvFileFinderOptions
     */
    private function setExcludedDirectories(array $excludedDirectories): EnvFileFinderOptions
    {
        $this->excludedDirectories = $excludedDirectories;
        return $this;
    }

    /**
     * @return array
     */
    public function getExcludedFiles(): array
    {
        return $this->excludedFiles;
    }

    /**
     * @param array $excludedFiles
     * @return EnvFileFinderOptions
     */
    private function setExcludedFiles(array $excludedFiles): EnvFileFinderOptions
    {
        $this->excludedFiles = $excludedFiles;
        return $this;
    }
}