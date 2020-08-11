<?php

declare(strict_types=1);

namespace LDL\Env\Writer;

use LDL\Env\Config\EnvConfig;

interface EnvFileWriterInterface
{
    /**
     * @param EnvConfig $config
     * @param string $content
     */
    public function write(EnvConfig $config, string $content): void;

    /**
     * @return Options\EnvWriterOptions
     */
    public function getOptions(): Options\EnvWriterOptions;
}