<?php

declare(strict_types=1);

namespace LDL\Env\Compiler;

use LDL\Env\Compiler\Exception\DuplicateKeyException;
use LDL\Env\Compiler\Line\Factory\EnvLineCompilerCompilerFactory;
use LDL\Env\Compiler\Options\EnvCompilerOptions;
use LDL\Env\Reader\Line\EnvLine;
use LDL\Env\Reader\EnvReader;
use LDL\Env\Reader\EnvReaderInterface;
use LDL\Env\Reader\Options\EnvReaderOptions;
use LDL\FS\Type\AbstractFileType;
use LDL\FS\Type\Types\Generic\Collection\GenericFileCollection;
use Symfony\Component\String\UnicodeString;

class EnvCompiler implements EnvCompilerInterface
{
    private const DEFAULT_ENV_CONFIG_HEADER = '#!--LDL_ENV_BUILDER_CONFIG ';

    /**
     * @var Options\EnvCompilerOptions
     */
    private $options;

    /**
     * @var array
     */
    private $contents = [];

    /**
     * @var \DateTimeInterface
     */
    private $date;

    public function __construct(Options\EnvCompilerOptions $options = null, \DateTimeInterface $date = null)
    {
        $this->options = $options ?? Options\EnvCompilerOptions::fromArray([]);
        $this->date = $date ?? new \DateTime('now');
    }

    /**
     * {@inheritdoc}
     */
    public function compile(
        GenericFileCollection $files,
        EnvReaderInterface $reader = null
    ) : string
    {
        $headers = [];
        /**
         * @var AbstractFileType $file
         */
        foreach($files as $file){
            $readerOptions  = EnvReaderOptions::fromArray([
               'file' => $file
            ]);

            $filePath = $file->getRealPath();

            $reader = $reader ?? new EnvReader();
            $lines = $reader->read($readerOptions);

            $options = clone $this->options;
            $firstLine = $lines[0]->getValue();

            if($firstLine->startsWith(self::DEFAULT_ENV_CONFIG_HEADER)){
                $options = EnvCompilerOptions::fromArray(
                    array_merge($this->options->toArray(), $this->parseEnvConfig($firstLine))
                );

                $headers[$filePath] = $this->formatEnvOptions($this->parseEnvConfig($firstLine));
            }

            if($options->getOnBeforeCompile()){
                $options->getOnBeforeCompile()($file, $lines);
            }

            /**
             * @var EnvLine $line
             */
            foreach($lines as $line){

                if($line->getValue()->startsWith(self::DEFAULT_ENV_CONFIG_HEADER)){
                    continue;
                }

                $compiler = EnvLineCompilerCompilerFactory::build($line);

                try{
                    $compiled = $compiler->compile($options, $file, $this->contents);
                    $this->contents[$filePath][] = $compiled;

                    if($options->getOnCompile()){
                        $options->getOnCompile()($compiled, $this->contents);
                    }
                }catch(DuplicateKeyException $e){

                    if(false === $options->allowVariableOverwrite()){
                        throw $e;
                    }

                    continue;
                }

            }

            if($options->getOnAfterCompile()){
                $options->getOnAfterCompile()($file, $lines);
            }

            $return = [];

            foreach($this->contents as $filePath => $vars){
                if($options->commentsEnabled()){
                    $return[] = "\n#Taken from $filePath\n";

                    if(array_key_exists($filePath, $headers)){
                        $return[] = $headers[$filePath]."\n";
                    }
                }

                $return[] = implode("\n",$vars);
            }
        }

        $firstLine = sprintf('#Generated date: %s', $this->date->format(\DateTimeInterface::W3C));

        array_unshift($return, $firstLine);

        return implode("\n",$return);
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions(): Options\EnvCompilerOptions
    {
        return $this->options;
    }

    private function parseEnvConfig(UnicodeString $line): array
    {
        $string = substr((string) $line, strlen(self::DEFAULT_ENV_CONFIG_HEADER));
        $params = explode(', ', $string);

        $result = [];

        foreach($params as $param){
            $values = explode('=', $param);
            switch ($values[0]){
                case 'PREFIX':
                    $result['prefix'] = strtoupper($values[1]);
                    break;
                case 'DEPTH':
                    $result['prefixDepth'] = (int) $values[1];
                    break;
                case 'UPPERCASE':
                    $result['convertToUpperCase'] = filter_var($values[1], FILTER_VALIDATE_BOOLEAN);
                    break;
                case 'COMMENTS':
                    $result['removeComments'] = filter_var($values[1], FILTER_VALIDATE_BOOLEAN);
                    break;
                case 'SYNTAX_ERROR':
                    $result['ignoreSyntaxErrors'] = filter_var($values[1], FILTER_VALIDATE_BOOLEAN);
                    break;
            }
        }

        return $result;
    }

    private function formatEnvOptions(array $options): string
    {
        $result = [];

        foreach($options as $key => $value){
            switch ($key){
                case 'prefix':
                    $result['PREFIX'] = $value;
                    break;
                case 'prefixDepth':
                    $result['DEPTH'] = (int) $value;
                    break;
                case 'convertToUpperCase':
                    $result['UPPERCASE'] = $value ? 'true' : 'false';
                    break;
                case 'removeComments':
                    $result['COMMENTS'] = $value ? 'true' : 'false';
                    break;
                case 'ignoreSyntaxErrors':
                    $result['SYNTAX_ERROR'] = $value ? 'true' : 'false';
                    break;
            }
        }

        return '#LDL_ENV_BUILDER_CONFIG '.http_build_query($result,'',', ');
    }
}
