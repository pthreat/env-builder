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

    public function compile(
        GenericFileCollection $files,
        EnvCompilerOptions $options = null
    ) : string
    {
        $this->options = $options ?? Options\EnvCompilerOptions::fromArray([]);

        foreach($files as $file){
            $this->contents[$file->getRealPath()] = $this->parse($file);
        }

        $contents = '';

        foreach($this->contents as $file => &$vars) {
            if ($this->options->commentsEnabled()) {
                $comment = "# Taken from: $file\n";
                $contents .= $comment;
            }

            foreach($vars as $key=>$value) {
                $contents .= strpos($key, 'comment') !== false ? $value : "$key=$value";
                $contents .= "\n";
            }

            $contents .= "\n";
        }

        return $contents;
    }


    private function parse(\SplFileInfo $file) : array
    {
        $contents = [];

        $fp = fopen($file->getRealPath(),'rb');

        $lineNo = 1;

        while($line = fgets($fp)){

            $line = trim($line);

            $firstCharacter = $line[0];

            if('#' === $firstCharacter && false === $this->options->removeComments()){
                $key = "comment_$lineNo";
                $contents[$key] = $line;
                $lineNo++;
                continue;
            }

            if('#' === $firstCharacter && true === $this->options->removeComments()){
                $lineNo++;
                continue;
            }

            $kv = $this->getKeyValue($file, $line, $lineNo);

            if(count($kv) === 0){
                $lineNo++;
                continue;
            }

            $key = $kv['key'];

            if(true === $this->options->isPrefixVariableWithFileName()){
                $dir = basename(dirname($file->getRealPath()));

                $key = sprintf(
                    '%s_%s',
                    true === $this->options->convertToUpperCase() ? strtoupper($dir) : $dir,
                    $kv['key']
                );
            }

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
            'value' => substr($line, $equalsPosition+1)
        ];
    }

    private function checkDuplicateKey(string $key, int $lineNo, \SplFileInfo $currentFile) : void
    {
        foreach($this->contents as $file => $content){
            if(!array_key_exists($key, $content)){
                continue;
            }

            $msg = sprintf(
                'Duplicate key "%s" found in file: "%s", at line: %s", previously defined in file "%s"',
                $key,
                $currentFile->getRealPath(),
                $lineNo,
                $file
            );

            throw new Exception\DuplicateKeyException($msg);
        }
    }
}