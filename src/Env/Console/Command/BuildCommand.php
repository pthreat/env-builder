<?php

namespace LDL\Env\Console\Command;

use LDL\Env\Writer\Exception\FileAlreadyExistsException;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use LDL\Console\Helper\ProgressBarFactory;
use LDL\Env\Builder\EnvBuilder;
use LDL\Env\Compiler\EnvCompiler;
use LDL\Env\Compiler\Options\EnvCompilerOptions;
use LDL\Env\Finder\EnvFileFinder;
use LDL\Env\Finder\Exception\NoFilesFoundException;
use LDL\Env\Finder\Options\EnvFileFinderOptions;
use LDL\Env\Writer\EnvFileWriter;
use LDL\Env\Writer\Options\EnvWriterOptions;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class BuildCommand extends SymfonyCommand
{
    public const COMMAND_NAME = 'env:build';

    protected const DEFAULT_DIRECTORIES = [];
    protected const DEFAULT_SCAN_FILES = ['.env'];
    protected const DEFAULT_FORCE_FILE_OVERWRITE = false;
    protected const DEFAULT_ALLOW_VARIABLE_OVERWRITE = false;
    protected const DEFAULT_IGNORE_SYNTAX_ERROR = false;
    protected const DEFAULT_PREFIX_VARIABLE = false;
    protected const DEFAULT_PREFIX_DEPTH = 1;
    protected const DEFAULT_CONVERT_TO_UPPERCASE = true;
    protected const DEFAULT_COMMENTS_ENABLED = true;

    public function configure() : void
    {
        $this->setName(self::COMMAND_NAME)
            ->setDescription('Build compiled .env file')
            ->addArgument(
                'output-file',
                InputArgument::REQUIRED,
                'Name of the output file'
            )
            ->addArgument(
                'output-directory',
                InputArgument::OPTIONAL,
                'Directory of the output file'
            )
            ->addOption(
                'force',
                'f',
                InputOption::VALUE_OPTIONAL,
                'Overwrite output file',
                self::DEFAULT_FORCE_FILE_OVERWRITE
            )
            ->addOption(
                'scan-directories',
                'd',
                InputOption::VALUE_OPTIONAL,
                sprintf(
                    'Comma separated list of directories to scan, default: %s',
                    implode(', ', self::DEFAULT_DIRECTORIES)
                ),
                self::DEFAULT_DIRECTORIES
            )
            ->addOption(
                'scan-files',
                'l',
                InputOption::VALUE_OPTIONAL,
                sprintf(
                    'Comma separated list of files to scan, default: %s',
                    implode(', ', self::DEFAULT_SCAN_FILES)
                ),
                self::DEFAULT_SCAN_FILES
            )
            ->addOption(
                'variable-overwrite',
                'o',
                InputOption::VALUE_OPTIONAL,
                'Allow variable overwrite',
                self::DEFAULT_ALLOW_VARIABLE_OVERWRITE
            )
            ->addOption(
                'ignore-syntax-error',
                'e',
                InputOption::VALUE_OPTIONAL,
                'Ignore syntax error',
                self::DEFAULT_IGNORE_SYNTAX_ERROR
            )
            ->addOption(
                'prefix-variable',
                'v',
                InputOption::VALUE_OPTIONAL,
                'Prefix variable',
                self::DEFAULT_PREFIX_VARIABLE
            )
            ->addOption(
                'prefix-depth',
                'p',
                InputOption::VALUE_OPTIONAL,
                'Prefix depth',
                self::DEFAULT_PREFIX_DEPTH
            )
            ->addOption(
                'convert-to-uppercase',
                'u',
                InputOption::VALUE_OPTIONAL,
                'Convert variables to uppercase',
                self::DEFAULT_CONVERT_TO_UPPERCASE
            )
            ->addOption(
                'comments-enabled',
                'c',
                InputOption::VALUE_OPTIONAL,
                'Comments enabled on the result file',
                self::DEFAULT_COMMENTS_ENABLED
            );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->build($input, $output);
        }catch(\Exception $e){
            $output->writeln("<error>{$e->getMessage()}</error>");
        }
    }

    private function build(
        InputInterface $input,
        OutputInterface $output
    ) : void
    {
        $start = hrtime(true);

        $outputFile = $input->getArgument('output-file');
        $outputDirectory = $input->getArgument('output-directory');
        $forceFileOverwrite = $input->getOption('output-overwrite');
        $scanDirectories = $input->getOption('scan-directories');
        $scanFiles = $input->getOption('scan-files');
        $variableOverwrite = $input->getOption('variable-overwrite');
        $ignoreSyntaxErrors = $input->getOption('ignore-syntax-error');
        $prefixVariable = $input->getOption('prefix-variable');
        $prefixDepth = $input->getOption('prefix-depth');
        $convertToUpper = $input->getOption('convert-to-uppercase');
        $convertsEnabled = $input->getOption('comments-enabled');

        try{
            $finderOptions = EnvFileFinderOptions::fromArray([
                'directories' => $scanDirectories,
                'files' => $scanFiles
            ]);

            $compilerOptions = EnvCompilerOptions::fromArray([
                'allowVariableOverwrite' => $variableOverwrite,
                'ignoreSyntaxErrors' => $ignoreSyntaxErrors,
                'prefixVariableWithFileName' => $prefixVariable,
                'prefixDepth' => $prefixDepth,
                'convertToUpperCase' => $convertToUpper,
                'commentsEnabled' => $convertsEnabled
            ]);

            $writerOptions = EnvWriterOptions::fromArray([
                'filename' => $outputFile,
                'directory' => $outputDirectory,
                'force' => $forceFileOverwrite
            ]);
        }catch(\Exception $e){
            $output->writeln("\n<error>Could not start building!</error>\n");
            $output->writeln("\n<error>{$e->getMessage()}</error>\n");

            return;
        }

        $finderService = new EnvFileFinder($finderOptions);
        $compilerService = new EnvCompiler($compilerOptions);
        $writerService = new EnvFileWriter($writerOptions);

        $builder = new EnvBuilder($finderService, $compilerService, $writerService);

        try {

            $title = '[ Building compiled env file ]';

            $output->writeln("\n<info>$title</info>\n");

            $progressBar = ProgressBarFactory::build($output);
            $progressBar->start();

            $builder->build();

            $output->writeln("");

        }catch(NoFilesFoundException $e){

            $output->writeln("\n<error>Build failed!</error>\n");
            $output->writeln("\n<error>{$e->getMessage()}</error>\n");

        }catch(FileAlreadyExistsException $e){

            $output->writeln("\n<error>Build failed!</error>\n");
            $output->writeln("\n<error>{$e->getMessage()}</error>\n");

        }

        $progressBar->finish();

        $end = hrtime(true);
        $total = round((($end - $start) / 1e+6) / 1000,2);

        $output->writeln("\n<info>Took: $total seconds</info>");
    }

}