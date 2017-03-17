<?php


namespace KamilleNaiveImporter\Importer;


use KamilleNaiveImporter\ImportSummary\ImportSummaryInterface;

interface KamilleImporterInterface
{


    /**
     * Return the importer id
     * @return string
     */
    public function getImporterId();

    /**
     * @param $moduleName
     * @return bool
     */
    public function canImport($moduleName);


    /**
     * @param $moduleName
     * @return array of flattened (cycling references resolved) dependencies, including the moduleName itself
     */
    public function getDependencyTree($moduleName);


    /**
     * Import the given module(s) in the class-modules directory.
     * It overwrites existing modules if force is set to true
     *
     * @return ImportSummaryInterface
     */
    public function import($modulesDir, $moduleName, $force=false);


    public function listAvailableModules();
}