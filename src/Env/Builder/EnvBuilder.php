<?php

declare(strict_types=1);

namespace LDL\Env\Builder;

use LDL\Env\Compiler\EnvCompiler;
use LDL\Env\Compiler\EnvCompilerInterface;
use LDL\Env\Finder\EnvFileFinder;
use LDL\Env\Finder\EnvFileFinderInterface;

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

    public function __construct(
        EnvFileFinderInterface $envFileFinder = null,
        EnvCompilerInterface $envCompiler = null
    )
    {
        $this->envFileFinder = $envFileFinder ?? new EnvFileFinder();
        $this->envCompiler = $envCompiler ?? new EnvCompiler();
    }

    /**
     * {@inheritdoc}
     */
    public function build(): string
    {
        $files = $this->envFileFinder->find();

        return $this->envCompiler->compile(
            $files
        );
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
}