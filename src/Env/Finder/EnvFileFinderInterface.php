<?php

declare(strict_types=1);

namespace LDL\Env\Finder;

use LDL\FS\Type\Types\Generic\Collection\GenericFileCollection;

interface EnvFileFinderInterface
{
    /**
     * @param bool $cache
     * @return GenericFileCollection
     * @throws Exception\NoFilesFoundException
     */
    public function find(bool $cache = true) : GenericFileCollection;

    /**
     * @return Options\EnvFileFinderOptions
     */
    public function getOptions(): Options\EnvFileFinderOptions;
}