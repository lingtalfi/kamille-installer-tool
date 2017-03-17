<?php


namespace ProgramPrinter;


interface ProgramPrinterInterface
{
    public function error($msg, $br = true);

    public function success($msg, $br = true);

    public function warn($msg, $br = true);

    public function info($msg, $br = true);

    public function help();

    public function say($msg, $br = true);
}