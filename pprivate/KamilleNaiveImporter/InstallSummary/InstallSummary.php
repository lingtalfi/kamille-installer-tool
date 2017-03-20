<?php


namespace KamilleNaiveImporter\InstallSummary;




use KamilleNaiveImporter\ImportSummary\ImportSummary;

class InstallSummary extends ImportSummary implements InstallSummaryInterface
{

    private $newlyInstalledModules;
    private $alreadyInstalledModules;
    private $uninstalledModules;
    private $successfullyUninstalledModules;


    public function __construct()
    {
        parent::__construct();
        $this->newlyInstalledModules = [];
        $this->alreadyInstalledModules = [];
        $this->uninstalledModules = [];
        $this->successfullyUninstalledModules = [];
    }



    public function getNewlyInstalledModules()
    {
        return $this->newlyInstalledModules;
    }

    public function getAlreadyInstalledModules()
    {
        return $this->alreadyInstalledModules;
    }

    public function getUninstalledModules()
    {
        return $this->uninstalledModules;
    }

    public function getSuccessfullyUninstalledModules()
    {
        return $this->successfullyUninstalledModules;
    }


    public function setNewlyInstalledModules(array $newlyInstalledModules)
    {
        $this->newlyInstalledModules = $newlyInstalledModules;
        return $this;
    }

    public function setAlreadyInstalledModules(array $alreadyInstalledModules)
    {
        $this->alreadyInstalledModules = $alreadyInstalledModules;
        return $this;
    }

    public function setUninstalledModules(array $uninstalledModules)
    {
        $this->uninstalledModules = $uninstalledModules;
        return $this;
    }

    public function setSuccessfullyUninstalledModules(array $successfullyUninstalledModules)
    {
        $this->successfullyUninstalledModules = $successfullyUninstalledModules;
        return $this;
    }

    //--------------------------------------------
    //
    //--------------------------------------------

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

    public function addNewlyInstalledModule($module)
    {
        $this->newlyInstalledModules[] = $module;
        return $this;
    }

    public function addSuccessfullyUninstalledModule($module)
    {
        $this->successfullyUninstalledModules[] = $module;
        return $this;
    }
}