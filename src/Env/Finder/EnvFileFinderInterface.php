<?php

namespace LDL\Env\Finder;

use LDL\FS\Type\Types\Generic\Collection\GenericFileCollection;

interface EnvFileFinderInterface
{
    /**
     * @param Options\EnvFileFinderOptions $options
     * @return GenericFileCollection
     * @throws Exception\NoFilesFoundException
     */
    public function find(Options\EnvFileFinderOptions $options=null) : GenericFileCollection;
}