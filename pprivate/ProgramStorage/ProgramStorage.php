<?php


namespace ProgramStorage;


class ProgramStorage
{


    public static function create()
    {
        return new static();
    }

    public function setModulesDirRelativePath($path)
    {
        if (false !== file_put_contents($this->getStorageFile(), $path)) {
            return true;
        }
        return false;
    }


    public function getModulesDirRelativePath()
    {
        $file = $this->getStorageFile();
        if (file_exists($file)) {
            return trim(file_get_contents($file));
        }
        return false;
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    private function getStorageFile()
    {
        return __DIR__ . "/program-storage.txt";
    }
}