<?php

namespace LDL\Env\Compiler;

use LDL\Env\Compiler\Options\EnvCompilerOptions;
use LDL\FS\Type\Types\Generic\Collection\GenericFileCollection;

interface EnvCompilerInterface
{
    /**
     * @param GenericFileCollection $files
     * @param EnvCompilerOptions $options
     * @return string
     */
    public function compile(
        GenericFileCollection $files,
        EnvCompilerOptions $options = null
    ) : string;
}