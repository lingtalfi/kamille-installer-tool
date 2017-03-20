<?php


namespace KamilleNaiveImporter\ImportSummary;


class ImportSummary implements ImportSummaryInterface
{
    private $successful;
    private $reImportedModules;
    private $alreadyImportedModules;
    private $notImportedModules;
    private $errorMessages;


    public function __construct()
    {
        $this->reImportedModules = [];
        $this->notImportedModules = [];
        $this->alreadyImportedModules = [];
        $this->errorMessages = [];
        $this->successful = false;
    }

    public static function create()
    {
        return new static();
    }


    public function isSuccessful()
    {
        return $this->successful;
    }

    /**
     * Modules which have actually been replaced (overwritten)
     */
    public function getReimportedModules()
    {
        return $this->reImportedModules;
    }


    public function getNotImportedModules()
    {
        return $this->notImportedModules;
    }

    public function getAlreadyImportedModules()
    {
        return $this->alreadyImportedModules;
    }

    public function getErrorMessages()
    {
        return $this->errorMessages;
    }



    //--------------------------------------------
    //
    //--------------------------------------------
    public function setSuccessful($successful)
    {
        $this->successful = $successful;
        return $this;
    }

    public function setReimportedModules(array $reImportedModules)
    {
        $this->reImportedModules = $reImportedModules;
        return $this;
    }

    public function setNotImportedModules(array $notImportedModules)
    {
        $this->notImportedModules = $notImportedModules;
        return $this;
    }

    public function setAlreadyImportedModules(array $alreadyImportedModules)
    {
        $this->alreadyImportedModules = $alreadyImportedModules;
        return $this;
    }

    public function setErrorMessages(array $errorMessages)
    {
        $this->errorMessages = $errorMessages;
        return $this;
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    public function addAlreadyImportedModule($module)
    {
        $this->alreadyImportedModules[] = $module;
        return $this;
    }

    public function addNotImportedModule($module)
    {
        $this->notImportedModules[] = $module;
        return $this;
    }

    public function addReimportedModule($module)
    {
        $this->reImportedModules[] = $module;
        return $this;
    }

    public function addErrorMessage($msg)
    {
        $this->errorMessages[] = $msg;
        return $this;
    }


}