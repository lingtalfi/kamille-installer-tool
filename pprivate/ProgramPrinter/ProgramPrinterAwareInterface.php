<?php


namespace ProgramPrinter;


interface ProgramPrinterAwareInterface
{
    public function setProgramPrinter(ProgramPrinterInterface $printer);
}