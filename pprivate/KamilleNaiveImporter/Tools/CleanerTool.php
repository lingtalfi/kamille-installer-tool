<?php


namespace Tools;


use Bat\FileSystemTool;
use ProgramPrinter\ProgramPrinter;
use ProgramPrinter\ProgramPrinterInterface;

class CleanerTool
{
    private $printer;
    private $filesToBeCleaned;
    private $dirsToBeCleaned;


    public function __construct()
    {
        $this->filesToBeCleaned = [
            '.gitignore',
            '.DS_Store',
        ];
        $this->dirsToBeCleaned = [
            '.idea',
            '.git',
        ];
    }

    public static function create()
    {
        return new static();
    }

    public function setFilesToBeCleaned($filesToBeCleaned)
    {
        $this->filesToBeCleaned = $filesToBeCleaned;
        return $this;
    }

    public function setDirsToBeCleaned($dirsToBeCleaned)
    {
        $this->dirsToBeCleaned = $dirsToBeCleaned;
        return $this;
    }


    public function setPrinter(ProgramPrinterInterface $printer)
    {
        $this->printer = $printer;
        return $this;
    }


    public function clean($targetPlanetsDir)
    {
        $printer = $this->getPrinter();
        if (file_exists($targetPlanetsDir)) {

            $planets = $this->getPlanetNames($targetPlanetsDir);
            foreach ($planets as $planet) {

                $d = $targetPlanetsDir . "/" . $planet;

                foreach ($this->filesToBeCleaned as $_f) {
                    $f = $d . "/$_f";
                    if (file_exists($f)) {
                        unlink($f);
                    }
                }

                foreach ($this->dirsToBeCleaned as $_f) {
                    $f = $d . "/$_f";
                    if (is_dir($f)) {
                        FileSystemTool::remove($f);
                    }
                }
            }
        } else {
            $printer->error("target planets directory does not exist: $targetPlanetsDir");
        }
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    private function getPlanetNames($planetsDir)
    {

        $files = scandir($planetsDir);
        return array_filter($files, function ($v) {
            if (false !== strpos($v, '.')) {
                return false;
            }
            return true;
        });
    }

    private function getPrinter()
    {
        if (null === $this->printer) {
            $this->printer = ProgramPrinter::create();
        }
        return $this->printer;
    }
}