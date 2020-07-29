<?php

declare(strict_types=1);

namespace LDL\Env\Reader\Line;

use LDL\Type\Collection\Types\Object\ObjectCollection;
use LDL\Type\Exception\TypeMismatchException;

class EnvLineCollection extends ObjectCollection
{
    public function validateItem($item): void
    {
        parent::validateItem($item);

        if($item instanceof EnvLine){
            return;
        }

        $msg = sprintf(
            'In "%s", expected instance of class: %s, instance of: %s was given',
            __CLASS__,
            EnvLine::class,
            get_class($item)
        );

        throw new TypeMismatchException($msg);
    }
}