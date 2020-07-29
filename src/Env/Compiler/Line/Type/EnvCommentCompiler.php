<?php

declare(strict_types=1);

namespace LDL\Env\Compiler\Line\Type;

use LDL\Env\Compiler\Line\Interfaces\EnvCommentCompilerInterface;
use LDL\Env\Compiler\Options\EnvCompilerOptions;
use LDL\Env\Reader\Line\EnvLine;
use LDL\FS\Type\AbstractFileType;
use Symfony\Component\String\UnicodeString;

class EnvCommentCompiler implements EnvCommentCompilerInterface
{
    public const TYPE = 'comment';

    /**
     * @var UnicodeString
     */
    private $comment;

    public function __construct(
        EnvLine $comment
    )
    {
        $this->comment = $comment;
    }

    public function getValue() : UnicodeString
    {
        return $this->comment;
    }

    public function getType() : string
    {
        return self::TYPE;
    }

    public function compile(
        EnvCompilerOptions $options,
        AbstractFileType $file,
        array $contents = []
    ) : UnicodeString
    {
        if($options->removeComments()){
            return new UnicodeString();
        }

        return $this->comment->getValue();
    }

    public function __toString()
    {
        return (string)$this->comment;
    }
}