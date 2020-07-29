<?php

declare(strict_types=1);

namespace LDL\Env\Writer;

interface EnvFileWriterInterface
{
    /**
     * @param string $content
     */
    public function write(string $content): void;

    /**
     * @return Options\EnvWriterOptions
     */
    public function getOptions(): Options\EnvWriterOptions;
}