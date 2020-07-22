<?php

namespace LDL\Env\Compiler\Line\Interfaces;

use LDL\Env\Compiler\Options\EnvCompilerOptions;
use LDL\FS\Type\AbstractFileType;
use Symfony\Component\String\UnicodeString;

interface EnvLineCompilerInterface
{
    public function getType() : string;
    public function getValue() : UnicodeString;

    public function compile(
        EnvCompilerOptions $options,
        AbstractFileType $file,
        array $contents=[]
    ) : UnicodeString;
}