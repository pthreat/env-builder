<?php

declare(strict_types=1);

namespace LDL\Env\Builder;

use LDL\Env\Compiler\EnvCompiler;
use LDL\Env\Compiler\EnvCompilerInterface;
use LDL\Env\Finder\EnvFileFinder;
use LDL\Env\Finder\EnvFileFinderInterface;
use LDL\Env\Writer\EnvFileWriter;
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
        EnvFileFinderInterface $envFileFinder = null,
        EnvCompilerInterface $envCompiler = null,
        EnvFileWriterInterface $envFileWriter = null
    )
    {
        $this->envFileFinder = $envFileFinder ?? new EnvFileFinder();
        $this->envCompiler = $envCompiler ?? new EnvCompiler();
        $this->envFileWriter = $envFileWriter ?? new EnvFileWriter();
    }

    /**
     * {@inheritdoc}
     */
    public function build(): void
    {
        $files = $this->envFileFinder->find();

        $compiled = $this->envCompiler->compile(
            $files
        );

        $this->envFileWriter->write($compiled);
    }

    /**
     * {@inheritdoc}
     */
    public function getFinder(): EnvFileFinderInterface
    {
        return $this->envFileFinder;
    }

    /**
     * {@inheritdoc}
     */
    public function getCompiler(): EnvCompilerInterface
    {
        return $this->envCompiler;
    }

    /**
     * {@inheritdoc}
     */
    public function getWriter(): EnvFileWriterInterface
    {
        return $this->envFileWriter;
    }
}