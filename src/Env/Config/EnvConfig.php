<?php declare(strict_types=1);

namespace LDL\Env\Config;

use LDL\Env\Interfaces\OptionsInterface;

class EnvConfig implements OptionsInterface
{
    public const DEFAULT_OUTPUT_FILENAME = '.env-compiled';

    public const DEFAULT_GENERATED_FILENAME = 'env-config.json';

    /**
     * @var string
     */
    private $outputFilename = self::DEFAULT_OUTPUT_FILENAME;

    /**
     * @var string
     */
    private $generatedAs = self::DEFAULT_GENERATED_FILENAME;

    /**
     * @var array
     */
    private $finderOptions = [];

    /**
     * @var array
     */
    private $compilerOptions = [];

    /**
     * @var array
     */
    private $writerOptions = [];

    /**
     * @var array
     */
    private $files = [];

    /**
     * @var \DateTime
     */
    private $date;

    public static function fromArray(array $options) : self
    {
        $instance = new static();
        $defaults = get_object_vars($instance);
        $merge = array_replace_recursive($defaults, $options);

        return $instance->setOutputFilename($merge['generation']['outputFilename'])
            ->setGeneratedAs($merge['generation']['generatedAs'])
            ->setDate($merge['generation']['date'])
            ->setFinderOptions($merge['finder']['options'])
            ->setFiles($merge['finder']['files'])
            ->setCompilerOptions($merge['compiler']['options'])
            ->setWriterOptions($merge['writer']['options']);
    }

    /**
     * @return array
     */
    public function toArray() : array
    {
        return [
            'generation' => [
                'user' => function_exists('posix_getpwuid') ? posix_getpwuid(posix_geteuid())['name'] : 'UNKNOWN',
                'outputFilename' => $this->getOutputFilename(),
                'generatedAs' => $this->getGeneratedAs(),
                'date' => $this->getDate()->format(\DateTimeInterface::W3C)
            ],
            'finder' => [
                'options' => $this->getFinderOptions(),
                'files' => $this->getFiles()
            ],
            'compiler' => [
                'options' => $this->getCompilerOptions()
            ],
            'writer' => [
                'options' => $this->getWriterOptions()
            ]
        ];
    }

    /**
     * @return array
     */
    public function jsonSerialize() : array
    {
        return $this->toArray();
    }

    /**
     * @return string
     */
    public function getOutputFilename(): string
    {
        return $this->outputFilename;
    }

    /**
     * @param string $outputFilename
     * @return EnvConfig
     */
    private function setOutputFilename(string $outputFilename): EnvConfig
    {
        $this->outputFilename = $outputFilename;
        return $this;
    }

    /**
     * @return string
     */
    public function getGeneratedAs(): string
    {
        return $this->generatedAs;
    }

    /**
     * @param string $generatedAs
     * @return EnvConfig
     */
    private function setGeneratedAs(string $generatedAs): EnvConfig
    {
        $this->generatedAs = $generatedAs;
        return $this;
    }

    /**
     * @return array
     */
    public function getFinderOptions(): array
    {
        return $this->finderOptions;
    }

    /**
     * @param array $finderOptions
     * @return EnvConfig
     */
    private function setFinderOptions(array $finderOptions): EnvConfig
    {
        $this->finderOptions = $finderOptions;
        return $this;
    }

    /**
     * @return array
     */
    public function getCompilerOptions(): array
    {
        return $this->compilerOptions;
    }

    /**
     * @param array $compilerOptions
     * @return EnvConfig
     */
    private function setCompilerOptions(array $compilerOptions): EnvConfig
    {
        $this->compilerOptions = $compilerOptions;
        return $this;
    }

    /**
     * @return array
     */
    public function getWriterOptions(): array
    {
        return $this->writerOptions;
    }

    /**
     * @param array $writerOptions
     * @return EnvConfig
     */
    private function setWriterOptions(array $writerOptions): EnvConfig
    {
        $this->writerOptions = $writerOptions;
        return $this;
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
     * @return EnvConfig
     */
    private function setFiles(array $files): EnvConfig
    {
        $this->files = $files;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDate(): \DateTime
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     * @return EnvConfig
     */
    private function setDate(\DateTime $date): EnvConfig
    {
        $this->date = $date;
        return $this;
    }
}