<?php

namespace LDL\Env\Compiler\Options;

class EnvCompilerOptions
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
     * @var bool
     */
    private $prefixVariableWithFileName = false;

    /**
     * @var int
     */
    private $prefixDepth=1;

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
            ->SetIgnoreSyntaxErrors($merge['ignoreSyntaxErrors'])
            ->setPrefixVariableWithFileName($merge['prefixVariableWithFileName'])
            ->setPrefixDepth($merge['prefixDepth'])
            ->setConvertToUpperCase($merge['convertToUpperCase'])
            ->setCommentsEnabled($merge['commentsEnabled'])
            ->setRemoveComments($merge['removeComments']);
    }

    /**
     * @return bool
     */
    public function isAllowVariableOverwrite(): bool
    {
        return $this->allowVariableOverwrite;
    }

    /**
     * @param bool $allowVariableOverwrite
     * @return EnvCompilerOptions
     */
    public function setAllowVariableOverwrite(bool $allowVariableOverwrite): EnvCompilerOptions
    {
        $this->allowVariableOverwrite = $allowVariableOverwrite;
        return $this;
    }

    /**
     * @return bool
     */
    public function isIgnoreSyntaxErrors(): bool
    {
        return $this->ignoreSyntaxErrors;
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
    public function allowVariableOverwrite(): bool
    {
        return $this->allowVariableOverwrite;
    }

    /**
     * @return bool
     */
    public function ignoreSyntaxErrors(): bool
    {
        return $this->ignoreSyntaxErrors;
    }

    /**
     * @return bool
     */
    public function isPrefixVariableWithFileName(): bool
    {
        return $this->prefixVariableWithFileName;
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
     * @param bool $ignoreSyntaxErrors
     * @return EnvCompilerOptions
     */
    private function setIgnoreSyntaxErrors(bool $ignoreSyntaxErrors): EnvCompilerOptions
    {
        $this->ignoreSyntaxErrors = $ignoreSyntaxErrors;
        return $this;
    }

    /**
     * @param bool $prefixVariableWithFileName
     * @return EnvCompilerOptions
     */
    private function setPrefixVariableWithFileName(bool $prefixVariableWithFileName): EnvCompilerOptions
    {
        $this->prefixVariableWithFileName = $prefixVariableWithFileName;
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
     * @return bool
     */
    public function removeComments(): bool
    {
        return $this->removeComments;
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
}