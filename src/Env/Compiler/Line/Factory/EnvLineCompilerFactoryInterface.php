<?php

namespace LDL\Env\Compiler\Line\Factory;

use LDL\Env\Reader\Line\EnvLine;
use LDL\Env\Compiler\Line\Interfaces\EnvLineCompilerInterface;

interface EnvLineCompilerFactoryInterface
{
    /**
     * Factory determines to which compiler the line must be compiled with
     *
     * @param EnvLine $line
     * @return EnvLineCompilerInterface
     */
    public static function build(EnvLine $line) : EnvLineCompilerInterface;
}