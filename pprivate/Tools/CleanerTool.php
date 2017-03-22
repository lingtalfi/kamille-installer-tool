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


    public function clean($targetDir, $recursive = false)
    {
        $printer = $this->getPrinter();
        if (file_exists($targetDir)) {

            $modules = $this->getModulesNames($targetDir);
            foreach ($modules as $module) {
                $d = $targetDir . "/" . $module;
                $this->cleanDir($d, $recursive);
            }
        } else {
            $printer->error("target planets directory does not exist: $targetDir");
        }
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    private function cleanDir($dir, $recursive = false)
    {
        foreach ($this->filesToBeCleaned as $_f) {
            $f = $dir . "/$_f";
            if (file_exists($f)) {
                unlink($f);
            }
        }
        foreach ($this->dirsToBeCleaned as $_f) {
            $f = $dir . "/$_f";
            if (is_dir($f)) {
                FileSystemTool::remove($f);
            }
        }

        if (true === $recursive) {
            $files = scandir($dir);
            foreach ($files as $f) {
                if ('.' !== $f && '..' !== $f) {
                    $file = $dir . "/" . $f;
                    if (is_dir($file)) {
                        $this->cleanDir($file, $recursive);
                    }
                }
            }
        }
    }


    private function getModulesNames($targetDir)
    {

        $files = scandir($targetDir);
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