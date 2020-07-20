<?php

namespace LDL\Env\Compiler;

use LDL\Env\Compiler\Options\EnvCompilerOptions;
use LDL\FS\Type\Types\Generic\Collection\GenericFileCollection;

class EnvCompiler implements EnvCompilerInterface
{
    /**
     * @var EnvCompilerOptions
     */
    private $options;

    private $contents = [];

    public function __construct(EnvCompilerOptions $options=null)
    {
        $this->options = $options ?? EnvCompilerOptions::fromArray([]);
    }

    public function compile(GenericFileCollection $files) : string
    {
        foreach($files as $file){
            $this->contents[$file->getRealPath()] = $this->parse($file);
        }

        if($this->options->commentsEnabled()){
            foreach($this->contents as $file => &$vars){
                $comment = "# Taken from: $file";
                array_unshift($vars, $comment);
            }
        }

        return implode("\n", $this->contents);
    }

    private function parse(\SplFileInfo $file) : array
    {
        $contents = [];

        $fp = fopen($file->getRealPath(),'rb');

        $lineNo = 1;

        while($line = fgets($fp)){

            $kv = $this->getKeyValue($file, $line, $lineNo);

            if(count($kv) === 0){
                $lineNo++;
                continue;
            }

            $key = $kv['key'];
            $value = $kv['value'];

            if(false === $this->options->allowVariableOverwrite()) {
                $this->checkDuplicateKey($key, $lineNo, $file);
            }

            if($this->options->convertToUpperCase()){
                $key = strtoupper($key);
            }

            $contents[$key] = $value;
            $lineNo++;
        }

        fclose($fp);

        return $contents;
    }

    private function getKeyValue(
        \SplFileInfo $file,
        string $line,
        int $lineNo
    ) : array
    {
        $equalsPosition = strpos($line, '=');

        if(false === $equalsPosition && !empty($line)){
            if(false === $this->options->ignoreSyntaxErrors()) {
                $msg = sprintf(
                    'Syntax error, in file: "%s", at line: %s, could not find = (equals) sign',
                    $file->getRealPath(),
                    $lineNo
                );

                throw new Exception\SyntaxErrorException($msg);
            }

            return [];
        }

        return [
            'key' => trim(substr($line, 0, $equalsPosition)),
            'value' => substr($line, $equalsPosition)
        ];
    }

    private function checkDuplicateKey(string $key, int $lineNo, \SplFileInfo $currentFile) : void
    {
        foreach($this->contents as $content){
            if(!array_key_exists($key, $content['vars'])){
                continue;
            }

            /**
             * @var \SplFileInfo $previousFile
             */
            $previousFile = $content['file'];
            $msg = sprintf(
                'Duplicate key "%s" found in file: "%s", at line: %s", previously defined in file "%s"',
                $key,
                $currentFile->getRealPath(),
                $previousFile->getRealPath(),
                $lineNo
            );

            throw new Exception\DuplicateKeyException($msg);
        }
    }

    public function __toString()
    {

    }

}