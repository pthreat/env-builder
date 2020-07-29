<?php

declare(strict_types=1);

namespace LDL\Env\Interfaces;

interface OptionsInterface extends \JsonSerializable
{
    /**
     * @return array
     */
    public function toArray(): array;
}