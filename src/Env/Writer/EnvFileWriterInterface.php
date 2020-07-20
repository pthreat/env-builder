<?php

namespace LDL\Env\Writer;

interface EnvFileWriterInterface
{
    /**
     * @param string $content
     * @throws Exception\FileAlreadyExistsException
     */
    public function write(string $content): void;
}