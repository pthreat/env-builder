<?php

namespace LDL\Env\Compiler\Line\Type;

use LDL\Env\Compiler\Exception\DuplicateKeyException;
use LDL\Env\Compiler\Line\Interfaces\EnvVarCompilerInterface;
use LDL\Env\Compiler\Line\Type\Exception\MissingEqualsException;
use LDL\Env\Compiler\Options\EnvCompilerOptions;
use LDL\Env\Reader\Line\EnvLine;
use LDL\FS\Type\AbstractFileType;
use Symfony\Component\String\UnicodeString;

class EnvVarCompiler implements EnvVarCompilerInterface
{
    public const TYPE = 'var';

    /**
     * @var EnvLine
     */
    private $line;

    public function __construct(EnvLine $value)
    {
        $this->line = $value;
    }

    public function getType(): string
    {
        return self::TYPE;
    }

    public function getKey() : UnicodeString
    {
        $string = $this->line->getValue();
        $equalsPosition = $string->indexOf('=');

        if(null === $equalsPosition) {
            return new UnicodeString();
        }

        return new UnicodeString($string->slice(0, $equalsPosition));
    }


    public function getValue() : UnicodeString
    {
        $string = $this->line->getValue();
        $equalsPosition = $string->indexOf('=');

        if(null === $equalsPosition) {
            return new UnicodeString();
        }

        return new UnicodeString($string->slice($equalsPosition)->trimStart('='));
    }

    public function compile(
        EnvCompilerOptions $options,
        AbstractFileType $file,
        array $contents = []
    ) : UnicodeString
    {
        $key = $this->getKey();

        if(0 === $key->length() && $options->ignoreSyntaxErrors()){
            return new UnicodeString();
        }

        if(0 === $key->length() && !$options->ignoreSyntaxErrors()){
            $msg = sprintf(
                'Missing equals sign at file: %s, line: %s. Input: %s',
                $file->getRealPath(),
                $this->line->getLineNumber(),
                $this->line->getValue()
            );

            throw new MissingEqualsException($msg);
        }

        $value = $this->getValue();

        $prefix = '';

        if($options->getPrefixDepth() > 0) {
            $prefix = $this->getEnvKeyPrefix($file, $options->getPrefixDepth());
        }

        $key = new UnicodeString(
            sprintf(
            '%s%s',
            '' === $prefix ? '' : "{$prefix}_",
                $key
            )
        );

        if($options->convertToUpperCase()){
            $key = $key->upper();
        }

        if(false === $options->allowVariableOverwrite()){
            $this->checkDuplicateKey(
                $key,
                $this->line->getLineNumber(),
                $contents,
                $file
            );
        }

        return new UnicodeString(sprintf('%s=%s', $key, $value));
    }

    private function getEnvKeyPrefix(AbstractFileType $file, int $depth) : string
    {
        $prefix = [];

        $path = new UnicodeString($file->getPath());
        $pieces = array_reverse($path->split('/'));

        /**
         * @var UnicodeString $value
         */
        foreach($pieces as $key=>$value){
            if(0 === $value->length()){
                continue;
            }

            if($key === $depth){
                break;
            }

            $prefix[] = $value;
        }

        return implode('_', $prefix);
    }

    private function checkDuplicateKey(
        string $key,
        int $line,
        array $contents,
        AbstractFileType $file
    ) : void
    {
        foreach($contents as $f => $vars){
            if(array_key_exists($key, $vars)){
                $msg = sprintf(
                    'Duplicated key "%s" found in file: "%s", at line %s, defined first in file: %s',
                    $key,
                    $file->getRealPath(),
                    $line,
                    $f
                );

                throw new DuplicateKeyException($msg);
            }
        }
    }
}