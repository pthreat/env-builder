<?php

declare(strict_types=1);

namespace LDL\Env\Reader;

interface EnvReaderInterface
{
    /**
     * @param Options\EnvReaderOptions $options
     * @return Line\EnvLineCollection
     */
    public function read(Options\EnvReaderOptions $options) : Line\EnvLineCollection;
}