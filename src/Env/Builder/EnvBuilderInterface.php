<?php

declare(strict_types=1);

namespace LDL\Env\Builder;

use LDL\Env\Compiler\EnvCompilerInterface;
use LDL\Env\Finder\EnvFileFinderInterface;
use LDL\Env\Finder\Exception\NoFilesFoundException;
use LDL\Env\Writer\Exception\FileAlreadyExistsException;

interface EnvBuilderInterface
{
    /**
     * @return string
     * @throws NoFilesFoundException
     * @throws FileAlreadyExistsException
     */
    public function build(): string;

    /**
     * @return EnvFileFinderInterface
     */
    public function getFinder(): EnvFileFinderInterface;

    /**
     * @return EnvCompilerInterface
     */
    public function getCompiler(): EnvCompilerInterface;
}