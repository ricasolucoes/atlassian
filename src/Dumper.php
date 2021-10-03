<?php

namespace Atlassian;

use Symfony\Component\VarDumper\Dumper\CliDumper;
use Symfony\Component\VarDumper\Cloner\VarCloner;

class Dumper
{
    /**
     * Dump a value with elegance.
     *
     * @param mixed $value
     *
     * @return void
     */
    public static function dump($value): void
    {
        if (class_exists(CliDumper::class)) {
            $dumper = 'cli' === PHP_SAPI ? new CliDumper() : new HtmlDumper();
            $dumper->dump((new VarCloner())->cloneVar($value));
        } else {
            var_dump($value);
        }
    }

    /**
     * @return never
     */
    public static function dd($x)
    {
        array_map(
            function ($x) {
                (new Dumper())->dump($x);
            }, func_get_args()
        );
        die(1);
    }
}