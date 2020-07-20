<?php

namespace LDL\Env\Builder;

use LDL\Env\Finder\Exception\NoFilesFoundException;
use LDL\Env\Writer\Exception\FileAlreadyExistsException;

interface EnvBuilderInterface
{
    /**
     * @throws NoFilesFoundException
     * @throws FileAlreadyExistsException
     */
    public function build(): void;
}