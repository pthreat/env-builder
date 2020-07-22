<?php

namespace LDL\Env\Compiler;

use LDL\Console\Helper\ProgressBarFactory;
use LDL\Env\Compiler\Line\Factory\EnvLineCompilerCompilerFactory;
use LDL\Env\Compiler\Options\EnvCompilerOptions;
use LDL\Env\Reader\Line\EnvLine;
use LDL\Env\Reader\EnvReader;
use LDL\Env\Reader\EnvReaderInterface;
use LDL\Env\Reader\Options\EnvReaderOptions;
use LDL\FS\Type\AbstractFileType;
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
        EnvCompilerOptions $options = null,
        EnvReaderInterface $reader = null
    ) : string
    {
        $this->options = $options ?? Options\EnvCompilerOptions::fromArray([]);

        /**
         * @var AbstractFileType $file
         */
        foreach($files as $file){
            $options  = EnvReaderOptions::fromArray([
               'file' => $file
            ]);

            $filePath = $file->getRealPath();

            $reader = $reader ?? new EnvReader();

            $lines = $reader->read($options);

            if($this->options->getOnBeforeCompile()){
                $this->options->getOnBeforeCompile()($file, $lines);
            }

            /**
             * @var EnvLine $parser
             */
            foreach($lines as $line){
                $compiler = EnvLineCompilerCompilerFactory::build($line);
                $compiled = $compiler->compile($this->options, $file, $this->contents);
                $this->contents[$filePath][] = $compiler->compile($this->options, $file, $this->contents);

                if($this->options->getOnCompile()){
                    $this->options->getOnCompile()($compiled, $this->contents);
                }
            }

            if($this->options->getOnAfterCompile()){
                $this->options->getOnAfterCompile()($file, $lines);
            }

            $return = [];

            foreach($this->contents as $file => $vars){
                if($this->options->commentsEnabled()){
                    $return[] = "#Taken from $file";
                }

                $return[] = implode("\n",$vars);
            }
        }

        return implode("\n",$return);

    }
}
