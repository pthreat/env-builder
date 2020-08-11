<?php declare(strict_types=1);

namespace LDL\Env\Config;

use LDL\Env\Compiler\EnvCompilerInterface;
use LDL\Env\Finder\EnvFileFinderInterface;
use LDL\Env\Finder\Exception\NoFilesFoundException;
use LDL\Env\Writer\EnvFileWriterInterface;
use LDL\FS\Type\Types\Generic\Collection\GenericFileCollection;

class EnvConfigFactory
{
    public static function factory(
        EnvFileFinderInterface $envFileFinder,
        EnvCompilerInterface $envCompiler,
        EnvFileWriterInterface $envFileWriter,
        string $generatedAs = null,
        \DateTime $date = null
    ) : EnvConfig
    {
        $utcTZ = new \DateTimeZone("UTC");

        try{
            $envFiles = $envFileFinder->find(true);
        }catch(NoFilesFoundException $e){
            $envFiles = new GenericFileCollection();
        }

        $files = [];

        foreach($envFiles as $file){
            $files[] = (string) $file;
        }

        return EnvConfig::fromArray([
            'generation' => [
                'outputFilename' => $envFileWriter->getOptions()->getFilename(),
                'generatedAs' => $generatedAs ?? EnvConfig::DEFAULT_GENERATED_FILENAME,
                'date' => $date !== null ? $date->setTimezone($utcTZ) : new \DateTime("now", $utcTZ)
            ],
            'finder' => [
                'options' => $envFileFinder->getOptions()->toArray(),
                'files' => $files
            ],
            'compiler' => [
                'options' => $envCompiler->getOptions()->toArray()
            ],
            'writer' => [
                'options' => $envFileWriter->getOptions()->toArray()
            ]
        ]);
    }
}