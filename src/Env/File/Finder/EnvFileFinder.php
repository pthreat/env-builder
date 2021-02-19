<?php declare(strict_types=1);

namespace LDL\Env\File\Finder;

use LDL\File\Collection\ReadableFileCollection;
use LDL\File\Finder\Adapter\Local\Facade\LocalFileFinderFacade;
use LDL\File\Finder\FoundFile;
use LDL\File\Validator\Config\FileTypeValidatorConfig;
use LDL\File\Validator\FileNameValidator;
use LDL\File\Validator\FileTypeValidator;
use LDL\Validators\Chain\AndValidatorChain;
use LDL\Validators\Chain\OrValidatorChain;
use LDL\Validators\RegexValidator;

class EnvFileFinder implements EnvFileFinderInterface
{
    /**
     * @var Options\EnvFileFinderOptionsInterface
     */
    private $options;

    /**
     * @var ReadableFileCollection
     */
    private $files;

    public function __construct(Options\EnvFileFinderOptionsInterface $options = null)
    {
        $this->options = $options ?? Options\EnvFileFinderOptions::fromArray([]);
        $this->files = new ReadableFileCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function find(bool $cache = false) : ReadableFileCollection
    {
        if(true === $cache){
            return $this->files;
        }

        $this->files = $this->files->getEmptyInstance();

        $options = $this->options;

        $validators = new AndValidatorChain([
            new FileTypeValidator([FileTypeValidatorConfig::FILE_TYPE_REGULAR])
        ]);

        $filesNameChain = new OrValidatorChain();

        foreach($options->getFiles() as $file){
            $filesNameChain->append(new FileNameValidator($file));
        }

        $validators->append($filesNameChain);

        $excludedChain = new AndValidatorChain();

        if(count($options->getExcludedDirectories()) > 0){
            $excludedDirsNameChain = new OrValidatorChain();

            foreach($options->getExcludedDirectories() as $dir){
                $excludedDirsNameChain->append(new FileNameValidator($dir, true));
            }

            $dirExcludedChain = new AndValidatorChain([
                new FileTypeValidator([FileTypeValidatorConfig::FILE_TYPE_DIRECTORY]),
                $excludedDirsNameChain
            ]);

            $excludedChain->append($dirExcludedChain);
        }

        if(count($options->getExcludedFiles()) > 0){
            $excludedFilesNameChain = new OrValidatorChain();

            foreach($options->getExcludedFiles() as $file){
                $excludedFilesNameChain->append(new FileNameValidator($file, true));
            }

            $fileExcludedChain = new AndValidatorChain([
                new FileTypeValidator([FileTypeValidatorConfig::FILE_TYPE_REGULAR]),
                $excludedFilesNameChain
            ]);

            $excludedChain->append($fileExcludedChain);
        }

        $validators->append($excludedChain);

        $foundFiles = LocalFileFinderFacade::findResult(
            $options->getDirectories(),
            $validators
        );

        if(!count($foundFiles)){
            $msg = sprintf(
                'No files were found matching: "%s" in directories: "%s"',
                implode(', ', $options->getFiles()),
                implode(', ', $options->getDirectories())
            );

            throw new Exception\NoFilesFoundException($msg);
        }

        /**
         * @var FoundFile $foundFile
         */
        foreach($foundFiles as $foundFile){
            $this->files->append($foundFile->getPath());
        }

        return $this->files;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions(): Options\EnvFileFinderOptionsInterface
    {
        return $this->options;
    }

}