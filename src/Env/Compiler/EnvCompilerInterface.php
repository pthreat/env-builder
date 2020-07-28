<?php

namespace LDL\Env\Compiler;

use LDL\Env\Reader\EnvReaderInterface;
use LDL\FS\Type\Types\Generic\Collection\GenericFileCollection;

interface EnvCompilerInterface
{
    /**
     * @param GenericFileCollection $files
     * @param EnvReaderInterface|null $reader
     * @return string
     */
    public function compile(
        GenericFileCollection $files,
        EnvReaderInterface $reader = null
    ) : string;

    /**
     * @return Options\EnvCompilerOptions
     */
    public function getOptions(): Options\EnvCompilerOptions;
}