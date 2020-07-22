<?php

namespace LDL\Env\Reader;

use Symfony\Component\String\UnicodeString;

class EnvReader implements EnvReaderInterface
{
    public function read(Options\EnvReaderOptions $options) : Line\EnvLineCollection
    {
        $file = $options->getFile();
        $collection = new Line\EnvLineCollection();

        $fp = fopen($file->getRealPath(),'rb');
        $lineNo = 1;

        while($line = fgets($fp)) {
            $parser = new Line\EnvLine($lineNo, new UnicodeString($line));
            $collection->append($parser);
        }

        fclose($fp);

        return $collection;
    }

}