<?php

declare(strict_types=1);

namespace LDL\Env\Compiler\Line\Factory;

use LDL\Env\Compiler\Line\Interfaces\EnvLineCompilerInterface;
use LDL\Env\Compiler\Line\Type\EnvCommentCompiler;
use LDL\Env\Compiler\Line\Type\EnvEmptyLineCompiler;
use LDL\Env\Compiler\Line\Type\EnvVarCompiler;
use LDL\Env\Reader\Line\EnvLine;
use Symfony\Component\String\UnicodeString;


class EnvLineCompilerCompilerFactory implements EnvLineCompilerFactoryInterface
{
    public static function build(EnvLine $line) : EnvLineCompilerInterface
    {
        $string = $line->getValue()->trimStart()->trimEnd("\r\n");
        $return = new EnvLine($line->getLineNumber(), new UnicodeString($string->toString()));

        if($string->length() === 0){
            return new EnvEmptyLineCompiler();
        }

        if($string->startsWith('#')){
            return new EnvCommentCompiler($return);
        }

        return new EnvVarCompiler($return);
    }
}