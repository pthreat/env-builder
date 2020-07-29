<?php

declare(strict_types=1);

namespace LDL\Env\Reader\Options;

use LDL\FS\Type\AbstractFileType;

class EnvReaderOptions
{
    /**
     * @var AbstractFileType
     */
    private $file;

    public static function fromArray(array $options)
    {
        $obj = new static;

        return $obj->setFile($options['file']);
    }

    public function getFile() : AbstractFileType
    {
        return $this->file;
    }

    private function setFile(AbstractFileType $file) : EnvReaderOptions
    {
        $this->file = $file;
        return $this;
    }
}
