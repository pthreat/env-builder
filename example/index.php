<?php declare(strict_types=1);

use LDL\Env\Builder\EnvBuilder;
use LDL\Env\Compiler\EnvCompiler;
use LDL\Env\Compiler\Options\EnvCompilerOptions;
use LDL\Env\Config\EnvConfigFactory;
use LDL\Env\Finder\EnvFileFinder;
use LDL\Env\Finder\Options\EnvFileFinderOptions;
use LDL\Env\Writer\EnvFileWriter;
use LDL\Env\Writer\Options\EnvWriterOptions;
use LDL\Env\Reader\EnvReader;
use LDL\Env\Reader\Options\EnvReaderOptions;
use LDL\FS\Type\Types\Generic\GenericFileType;
use LDL\Env\Reader\Line\EnvLine;

require __DIR__.'/../vendor/autoload.php';

try{

    $finderOptions = EnvFileFinderOptions::fromArray([
        'directories' => [__DIR__.'/Application'],
        'files' => ['.env'],
        'excludedDirectories' => [],
    ]);

    $compilerOptions = EnvCompilerOptions::fromArray([
        'allowVariableOverwrite' => false,
        'ignoreSyntaxErrors' => false,
        'prefixDepth' => 0,
        'convertToUpperCase' => true,
        'commentsEnabled' => true,
        'removeComments' => false
    ]);

    $writerOptions = EnvWriterOptions::fromArray([
        'filename' => '.env-compiled',
        'force' => true
    ]);

    $writer = new EnvFileWriter($writerOptions);

    echo "[ Building compiled env file ]\n";

    $builder = new EnvBuilder(
        new EnvFileFinder($finderOptions),
        new EnvCompiler($compilerOptions)
    );

    $content = $builder->build();

    $writer->write(EnvConfigFactory::factory(
        $builder->getFinder(),
        $builder->getCompiler(),
        $writer
    ), $content);

}catch(\Exception $e) {

    echo "[ Build failed! ]\n";
    return;

}

echo "Load generated .env-compiled\n";

$env = new EnvReader();

$envReaderOptions = EnvReaderOptions::fromArray([
    'file' => new GenericFileType(__DIR__.'/../.env-compiled')
]);

$collection = $env->read($envReaderOptions);

echo "Get content of .env-compiled\n";

/**
 * @var EnvLine $envLine
 */
foreach($collection as $envLine){
    echo (string) $envLine->getValue();
}
