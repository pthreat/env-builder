<?php

namespace LDL\Env\Compiler\Line\Type;

use LDL\Env\Compiler\Line\Interfaces\EnvEmptyLineCompilerInterface;
use LDL\Env\Compiler\Options\EnvCompilerOptions;
use LDL\FS\Type\AbstractFileType;
use Symfony\Component\String\UnicodeString;

class EnvEmptyLineCompiler implements EnvEmptyLineCompilerInterface
{
    public const TYPE = 'empty';

    public function getType(): string
    {
        return self::TYPE;
    }

    public function getValue(): UnicodeString
    {
        return new UnicodeString('');
    }

    public function compile(
        EnvCompilerOptions $options,
        AbstractFileType $file,
        array $contents = []
    ) : UnicodeString
    {
        return $this->getValue();
    }

    public function __toString()
    {
        return '';
    }

}