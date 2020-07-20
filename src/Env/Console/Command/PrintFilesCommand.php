<?php

namespace LDL\Env\Console\Command;

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

    protected const DEFAULT_DIRECTORIES = [];
    protected const DEFAULT_SCAN_FILES = ['.env'];

    public function configure() : void
    {
        $this->setName(self::COMMAND_NAME)
            ->setDescription('Prints .env files')
            ->addOption(
                'scan-directories',
                'd',
                InputOption::VALUE_REQUIRED,
                sprintf(
                    'Comma separated list of directories to scan, default: %s',
                    implode(', ', self::DEFAULT_DIRECTORIES)
                ),
                self::DEFAULT_DIRECTORIES
            )
            ->addOption(
                'scan-files',
                'l',
                InputOption::VALUE_REQUIRED,
                sprintf(
                    'Comma separated list of files to scan, default: %s',
                    implode(', ', self::DEFAULT_SCAN_FILES)
                ),
                self::DEFAULT_SCAN_FILES
            );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->printServiceFiles($input, $output);
        }catch(\Exception $e){
            $output->writeln("<error>{$e->getMessage()}</error>");
        }
    }

    private function printServiceFiles(
        InputInterface $input,
        OutputInterface $output
    ) : void
    {
        $scanDirectories = $input->getOption('scan-directories');
        $scanFiles = $input->getOption('scan-files');

        $finder = EnvFileFinderOptions::fromArray([
            'directories' => $scanDirectories,
            'files' => $scanFiles
        ]);

        $finderService = new EnvFileFinder($finder);

        $total = 0;

        $output->writeln("<info>[ Env files list ]</info>\n");

        try{
            $files = $finderService->find();
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