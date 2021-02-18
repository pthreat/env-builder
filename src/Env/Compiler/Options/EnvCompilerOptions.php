<?php

declare(strict_types=1);

namespace LDL\Env\Compiler\Options;

use LDL\Env\Interfaces\OptionsInterface;

class EnvCompilerOptions implements OptionsInterface
{
    /**
     * @var bool
     */
    private $allowVariableOverwrite = false;

    /**
     * @var bool
     */
    private $ignoreSyntaxErrors = false;

    /**
     * @var string
     */
    private $prefix = '';

    /**
     * @var int
     */
    private $prefixDepth=0;

    /**
     * @var bool
     */
    private $convertToUpperCase=true;

    /**
     * @var bool
     */
    private $commentsEnabled = true;

    /**
     * @var bool
     */
    private $removeComments = false;

    /**
     * @var callable
     */
    private $onBeforeCompile;

    /**
     * @var callable
     */
    private $onCompile;

    /**
     * @var callable
     */
    private $onAfterCompile;

    private function __construct()
    {
    }

    public static function fromArray(array $options) : self
    {
        $instance = new static();
        $defaults = get_object_vars($instance);

        foreach($options as $opt=>$value){
            if(array_key_exists($opt, $defaults)) {
                continue;
            }
            $msg = sprintf(
                'Unknown option: "%s", valid options are: %s',
                $opt,
                implode(', ', array_keys($defaults))
            );

            throw new Exception\UnknownOptionException($msg);
        }

        $merge = array_merge($defaults, $options);

        return $instance->setAllowVariableOverwrite($merge['allowVariableOverwrite'])
            ->setIgnoreSyntaxErrors($merge['ignoreSyntaxErrors'])
            ->setPrefix($merge['prefix'])
            ->setPrefixDepth($merge['prefixDepth'])
            ->setConvertToUpperCase($merge['convertToUpperCase'])
            ->setCommentsEnabled($merge['commentsEnabled'])
            ->setRemoveComments($merge['removeComments'])
            ->setOnBeforeCompile($merge['onBeforeCompile'])
            ->setOnCompile($merge['onCompile'])
            ->setOnAfterCompile($merge['onAfterCompile']);
    }

    /**
     * @return array
     */
    public function toArray() : array
    {
        return get_object_vars($this);
    }

    /**
     * @return array
     */
    public function jsonSerialize() : array
    {
        return $this->toArray();
    }

    /**
     * @return callable|null
     */
    public function getOnBeforeCompile() : ?callable
    {
        return $this->onBeforeCompile;
    }

    /**
     * @return callable|null
     */
    public function getOnCompile() : ?callable
    {
        return $this->onCompile;
    }

    /**
     * @return callable|null
     */
    public function getOnAfterCompile() : ?callable
    {
        return $this->onAfterCompile;
    }

    /**
     * @return bool
     */
    public function allowVariableOverwrite(): bool
    {
        return $this->allowVariableOverwrite;
    }

    /**
     * @return bool
     */
    public function convertToUpperCase(): bool
    {
        return $this->convertToUpperCase;
    }

    /**
     * @return bool
     */
    public function ignoreSyntaxErrors(): bool
    {
        return $this->ignoreSyntaxErrors;
    }

    /**
     * @return string
     */
    public function getPrefix(): string
    {
        return $this->prefix;
    }

    /**
     * @return int
     */
    public function getPrefixDepth(): int
    {
        return $this->prefixDepth;
    }

    /**
     * @return bool
     */
    public function commentsEnabled(): bool
    {
        return $this->commentsEnabled;
    }

    /**
     * @return bool
     */
    public function removeComments(): bool
    {
        return $this->removeComments;
    }

    /**
     * @param bool $allowVariableOverwrite
     * @return EnvCompilerOptions
     */
    private function setAllowVariableOverwrite(bool $allowVariableOverwrite): EnvCompilerOptions
    {
        $this->allowVariableOverwrite = $allowVariableOverwrite;
        return $this;
    }

    /**
     * @param string $prefix
     * @return EnvCompilerOptions
     */
    private function setPrefix(string $prefix): EnvCompilerOptions
    {
        $this->prefix = $prefix;
        return $this;
    }

    /**
     * @param int $prefixDepth
     * @return EnvCompilerOptions
     */
    private function setPrefixDepth(int $prefixDepth): EnvCompilerOptions
    {
        $this->prefixDepth = $prefixDepth;
        return $this;
    }

    /**
     * @param bool $convertToUpperCase
     * @return EnvCompilerOptions
     */
    private function setConvertToUpperCase(bool $convertToUpperCase): EnvCompilerOptions
    {
        $this->convertToUpperCase = $convertToUpperCase;
        return $this;
    }

    /**
     * @param bool $enabled
     * @return EnvCompilerOptions
     */
    private function setCommentsEnabled(bool $enabled): EnvCompilerOptions
    {
        $this->commentsEnabled = $enabled;

        return $this;
    }

    /**
     * @param bool $removeComments
     * @return EnvCompilerOptions
     */
    private function setRemoveComments(bool $removeComments): EnvCompilerOptions
    {
        $this->removeComments = $removeComments;
        return $this;
    }

    /**
     * @param bool $ignoreSyntaxErrors
     * @return EnvCompilerOptions
     */
    private function setIgnoreSyntaxErrors(bool $ignoreSyntaxErrors): EnvCompilerOptions
    {
        $this->ignoreSyntaxErrors = $ignoreSyntaxErrors;
        return $this;
    }

    /**
     * @param callable $fn
     * @return EnvCompilerOptions
     */
    private function setOnAfterCompile(callable $fn=null) : EnvCompilerOptions
    {
        $this->onAfterCompile = $fn;
        return $this;
    }

    /**
     * @param callable $fn
     * @return EnvCompilerOptions
     */
    private function setOnCompile(callable $fn=null) : EnvCompilerOptions
    {
        $this->onCompile = $fn;
        return $this;
    }

    /**
     * @param callable|null $fn
     * @return EnvCompilerOptions
     */
    private function setOnBeforeCompile(callable $fn=null) : EnvCompilerOptions
    {
        $this->onBeforeCompile = $fn;
        return $this;
    }

}