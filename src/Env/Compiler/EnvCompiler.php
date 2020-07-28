<?php

namespace LDL\Env\Compiler;

use LDL\Env\Compiler\Line\Factory\EnvLineCompilerCompilerFactory;
use LDL\Env\Reader\Line\EnvLine;
use LDL\Env\Reader\EnvReader;
use LDL\Env\Reader\EnvReaderInterface;
use LDL\Env\Reader\Options\EnvReaderOptions;
use LDL\FS\Type\AbstractFileType;
use LDL\FS\Type\Types\Generic\Collection\GenericFileCollection;

class EnvCompiler implements EnvCompilerInterface
{
    /**
     * @var Options\EnvCompilerOptions
     */
    private $options;

    private $contents = [];

    public function __construct(Options\EnvCompilerOptions $options = null)
    {
        $this->options = $options ?? Options\EnvCompilerOptions::fromArray([]);
    }

    /**
     * {@inheritdoc}
     */
    public function compile(
        GenericFileCollection $files,
        EnvReaderInterface $reader = null
    ) : string
    {
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
                $this->contents[$filePath][] = $compiled;

                if($this->options->getOnCompile()){
                    $this->options->getOnCompile()($compiled, $this->contents);
                }
            }

            if($this->options->getOnAfterCompile()){
                $this->options->getOnAfterCompile()($file, $lines);
            }

            $return = [];

            foreach($this->contents as $filePath => $vars){
                if($this->options->commentsEnabled()){
                    $return[] = "#Taken from $filePath";
                }

                $return[] = implode("\n",$vars);
            }
        }

        return implode("\n",$return);
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions(): Options\EnvCompilerOptions
    {
        return $this->options;
    }
}
