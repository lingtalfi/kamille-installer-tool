<?php


namespace LocalUniverse;


/**
 * This class allows to get/set the value of the local universe path.
 * It uses a simple text file as the storage.
 * The text file is created next to the current script location (which is presumed to be next to the uni script.
 */
class LocalUniverse
{


    public static function create()
    {
        return new static();
    }

    public function setLocalUniversePath($path)
    {
        if (false !== file_put_contents($this->getStorageFile(), $path)) {
            return true;
        }
        return false;
    }


    public function getLocalUniversePath()
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
        return __DIR__ . "/local-universe-path.txt";
    }
}