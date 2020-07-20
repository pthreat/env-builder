<?php

namespace LDL\Env\Compiler;

use LDL\FS\Type\Types\Generic\Collection\GenericFileCollection;

interface EnvCompilerInterface
{
    /**
     * @param GenericFileCollection $files
     * @return string
     */
    public function compile(GenericFileCollection $files) : string;
}