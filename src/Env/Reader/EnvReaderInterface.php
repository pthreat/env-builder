<?php

namespace LDL\Env\Reader;

interface EnvReaderInterface
{
    /**
     * Returns a collection of UnicodeStrings
     * @param Options\EnvReaderOptions $options
     * @return Line\EnvLineCollection
     */
    public function read(Options\EnvReaderOptions $options) : Line\EnvLineCollection;
}