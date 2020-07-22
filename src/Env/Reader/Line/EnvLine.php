<?php

namespace LDL\Env\Reader\Line;

use Symfony\Component\String\UnicodeString;

class EnvLine
{
    /**
     * @var int
     */
    private $number;

    /**
     * @var UnicodeString
     */
    private $value;

    /**
     * EnvLine constructor.
     *
     * @param int $lineNumber
     * @param UnicodeString $string
     */
    public function __construct(int $lineNumber, UnicodeString $string)
    {
        $this->number = $lineNumber;
        $this->value = $string;
    }

    public function getValue() : UnicodeString
    {
        return $this->value;
    }

    public function getLineNumber() : int
    {
        return $this->number;
    }
}