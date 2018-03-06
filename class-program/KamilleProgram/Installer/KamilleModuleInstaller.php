<?php

namespace KamilleProgram\Installer;


use ApplicationItemManager\ApplicationItemManagerInterface;
use ApplicationItemManager\Aware\ApplicationItemManagerAwareInterface;
use ApplicationItemManager\Exception\ApplicationItemManagerException;
use ApplicationItemManager\Installer\LingAbstractItemInstaller;


class KamilleModuleInstaller extends LingAbstractItemInstaller
{

    private $widgetManager;

    public function __construct()
    {
        parent::__construct();
        $this->itemType = "module";
    }

    public function setWidgetManager(ApplicationItemManagerInterface $widgetManager)
    {
        $this->widgetManager = $widgetManager;
        return $this;
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


    protected function prepareItemInstaller($object)
    {
        if ($object instanceof ApplicationItemManagerAwareInterface) {
            $object->setApplicationItemManager($this->widgetManager);
        }
    }


}