<?php


namespace KamilleNaiveImporter\Importer;


interface ImporterInterface
{


    /**
     * @return string, the full name of the importer
     */
    public function getFullName();

    /**
     * @return array, an array of importer aliases
     */
    public function getAliases();

    public function setAliases(array $aliases);


    /**
     * @param $moduleName
     * @return bool
     */
    public function canImport($moduleName);


    /**
     * @param $moduleName
     * @return array of flattened (cycling references resolved) dependencies for the given module;
     * the dependency tree includes the moduleName itself.
     *
     * The order is not considered important.
     */
    public function getDependencyTree($moduleName);


    /**
     * Import the given module(s) in the given modulesDir directory.
     */
    public function import($moduleName, $modulesDir);


    /**
     * @return array, list of modules this importer is capable of fetching
     */
    public function getAvailableModules();
}