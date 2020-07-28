<?php

namespace LDL\Env\Finder;

use LDL\FS\Type\Types\Generic\Collection\GenericFileCollection;

interface EnvFileFinderInterface
{
    /**
     * @return GenericFileCollection
     * @throws Exception\NoFilesFoundException
     */
    public function find() : GenericFileCollection;

    /**
     * @return Options\EnvFileFinderOptions
     */
    public function getOptions(): Options\EnvFileFinderOptions;
}