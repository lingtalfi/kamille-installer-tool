<?php

namespace KamilleProgram\Installer;


use ApplicationItemManager\Exception\ApplicationItemManagerException;
use ApplicationItemManager\Installer\LingAbstractItemInstaller;


class KamilleModuleInstaller extends LingAbstractItemInstaller
{

    public function __construct()
    {
        parent::__construct();
        $this->itemType = "module";
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    protected function getInstallerClass($itemName)
    {
        return 'Module\\' . $itemName . '\\' . $itemName . "Module";
    }

    protected function getFile()
    {
        if (null === $this->applicationDirectory) {
            throw new ApplicationItemManagerException("Set applicationDirectory first");
        }
        return $this->applicationDirectory . "/modules.txt";
    }

}