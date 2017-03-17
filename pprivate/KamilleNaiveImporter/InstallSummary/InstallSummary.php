<?php


namespace KamilleNaiveImporter\ImportSummary;


use KamilleNaiveImporter\InstallSummary\InstallSummaryInterface;

class InstallSummary extends ImportSummary implements InstallSummaryInterface
{

    private $reinstalledModules;
    private $alreadyInstalledModules;
    private $uninstalledModules;
    private $successfullyUninstalledModules;


    public function __construct()
    {
        parent::__construct();
        $this->reinstalledModules = [];
        $this->alreadyInstalledModules = [];
        $this->uninstalledModules = [];
        $this->successfullyUninstalledModules = [];
    }


    public function getReinstalledModules()
    {
        return $this->reinstalledModules;
    }

    public function getAlreadyInstalledModules()
    {
        return $this->alreadyInstalledModules;
    }

    public function getUninstalledModules()
    {
        return $this->uninstalledModules;
    }

    public function getSuccessfullyUninstalled()
    {
        return $this->successfullyUninstalledModules;
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    public function addAlreadyInstalledModule($module)
    {
        $this->alreadyInstalledModules[] = $module;
        return $this;
    }

    public function addUninstalledModule($module)
    {
        $this->uninstalledModules[] = $module;
        return $this;
    }

    public function addReinstalledModule($module)
    {
        $this->reinstalledModules[] = $module;
        return $this;
    }
}