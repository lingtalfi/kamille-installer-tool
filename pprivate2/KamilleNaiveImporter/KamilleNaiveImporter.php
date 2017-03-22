<?php


namespace KamilleNaiveImporter;


use KamilleNaiveImporter\Importer\ImporterInterface;

class KamilleNaiveImporter
{
    private $importers;


    public function __construct()
    {
        $this->importers = [];
    }


    public static function create()
    {
        return new static();
    }


    public function addImporter(ImporterInterface $importer)
    {
        $this->importers[] = $importer;
        return $this;
    }


    public function import($moduleName, $force = false)
    {
        /**
         * Todo: first collect the tree.
         * If one of the dependencies is going to miss (canImport=false),
         * ask the user what to do: import and ignore missing dependencies, or abort.
         */
    }

}