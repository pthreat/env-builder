<?php

namespace LDL\Env\Builder;

use LDL\Env\Compiler\EnvCompilerInterface;
use LDL\Env\Finder\EnvFileFinderInterface;
use LDL\Env\Writer\EnvFileWriterInterface;

class EnvBuilder implements EnvBuilderInterface
{
    /**
     * @var EnvFileFinderInterface
     */
    private $envFileFinder;

    /**
     * @var EnvCompilerInterface
     */
    private $envCompiler;

    /**
     * @var EnvFileWriterInterface
     */
    private $envFileWriter;

    public function __construct(
        EnvFileFinderInterface $envFileFinder,
        EnvCompilerInterface $envCompiler,
        EnvFileWriterInterface $envFileWriter
    )
    {
        $this->envFileFinder = $envFileFinder;
        $this->envCompiler = $envCompiler;
        $this->envFileWriter = $envFileWriter;
    }

    /**
     * {@inheritdoc}
     */
    public function build(): void
    {
        $files = $this->envFileFinder->find();

        $compiled = $this->envCompiler->compile($files);

        $this->envFileWriter->write($compiled);
    }
}