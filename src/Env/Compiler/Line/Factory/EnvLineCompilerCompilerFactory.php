<?php

namespace LDL\Env\Compiler\Line\Factory;

use LDL\Env\Compiler\Line\Interfaces\EnvLineCompilerInterface;
use LDL\Env\Compiler\Line\Type\EnvCommentCompiler;
use LDL\Env\Compiler\Line\Type\EnvEmptyLineCompiler;
use LDL\Env\Compiler\Line\Type\EnvVarCompiler;
use LDL\Env\Reader\Line\EnvLine;


class EnvLineCompilerCompilerFactory implements EnvLineCompilerFactoryInterface
{
    public static function build(EnvLine $line) : EnvLineCompilerInterface
    {
        $string = $line->getValue()->trimStart()->trimEnd("\r\n");

        if($string->length() === 0){
            return new EnvEmptyLineCompiler();
        }

        if($string->startsWith('#')){
            return new EnvCommentCompiler($line);
        }

        return new EnvVarCompiler($line);
    }
}