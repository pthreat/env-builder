<?php

namespace LDL\Env\Console\Command;

use LDL\Env\Finder\EnvFileFinderInterface;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use LDL\Env\Finder\EnvFileFinder;
use LDL\Env\Finder\Exception\NoFilesFoundException;
use LDL\Env\Finder\Options\EnvFileFinderOptions;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\SplFileInfo as FileInfo;

class PrintFilesCommand extends SymfonyCommand
{
    public const COMMAND_NAME = 'env:print';

    /**
     * @var EnvFileFinderInterface
     */
    private $finder;

    public function __construct(?string $name = null, EnvFileFinderInterface $finder=null)
    {
        parent::__construct($name);

        if(null === $finder){
            $finder = new EnvFileFinder();
        }

        $this->finder = $finder;
    }

    public function configure() : void
    {
        $defaults = EnvFileFinderOptions::fromArray([]);

        $defaultDirectories = implode(', ', $defaults->getDirectories());
        $defaultFiles = implode(', ', $defaults->getFiles());

        $this->setName(self::COMMAND_NAME)
            ->setDescription('Prints .env files')
            ->addOption(
                'scan-directories',
                'd',
                InputOption::VALUE_REQUIRED,
                sprintf(
                    'Comma separated list of directories to scan, default: %s',
                    $defaultDirectories
                ),
                $defaultDirectories
            )
            ->addOption(
                'scan-files',
                'l',
                InputOption::VALUE_REQUIRED,
                sprintf(
                    'Comma separated list of files to scan, default: %s',
                    $defaultFiles
                ),
                $defaultFiles
            );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->printFiles($input, $output);
            return 0;
        }catch(\Exception $e){
            $output->writeln("<error>{$e->getMessage()}</error>");
            return 1;
        }
    }

    private function printFiles(
        InputInterface $input,
        OutputInterface $output
    ) : void
    {
        $total = 0;

        $output->writeln("<info>[ Env files list ]</info>\n");

        try{

            $files = $this->finder->find(
                EnvFileFinderOptions::fromArray([
                    'directories' => explode(',', $input->getOption('scan-directories')),
                    'files' => explode(',', $input->getOption('scan-files'))
                ])
            );

        }catch(NoFilesFoundException $e){
            $output->writeln("\n<error>{$e->getMessage()}</error>\n");

            return;
        }

        /**
         * @var FileInfo $file
         */
        foreach($files as $file){
            $total++;
            $output->writeln($file->getRealPath());
        }

        $output->writeln("\n<info>Total files: $total</info>");
    }

}