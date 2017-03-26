<?php


namespace Kamille\Utils\KamilleNaiveImporter;


interface KamilleNaiveImporterInterface
{


    /**
     * @return array of installed modules
     */
    public function getInstalledModulesList();

    /**
     * @return array of imported module names
     */
    public function getImportedModulesList();

    /**
     * @return array, an array of importer fullName => array of module names
     */
    public function getAvailableModulesList($moduleAlias = null);


    /**
     * @return array, an array of importer fullName => array of module names
     */
    public function search($search, $searchInDescriptionToo = false, $moduleAlias = null);


    public function isInstalled($moduleName);


    public function import($moduleName, $modulesDir, $force = false);

    public function install($moduleName, $modulesDir, $force = false);


    public function uninstall($moduleName, $modulesDir);


}