<?php


namespace Kamille\Utils\KamilleNaiveImporter\Importer;


abstract class AbstractHardcodedImporter extends AbstractImporter
{

    private $dependencyMap;


    public function __construct()
    {
        parent::__construct();
        $this->dependencyMap = $this->getDependencyMap();
    }


    //--------------------------------------------
    // OVERRIDE THIS METHOD
    //--------------------------------------------
    protected function getDependencyMap()
    {
        return [];
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    public function getDependencyTree($moduleName)
    {
        $tree = [];
        $this->collectDependencyTree($moduleName, $tree);
        $tree = array_unique($tree);
        return $tree;
    }

    public function getAvailableModules()
    {
        return array_keys($this->dependencyMap);
    }


    public function canImport($moduleName)
    {
        return array_key_exists($moduleName, $this->dependencyMap);
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    private function collectDependencyTree($modules, array &$tree)
    {
        if (is_string($modules)) {
            $moduleName = $modules;
            $tree[] = $moduleName;
            if (array_key_exists($moduleName, $this->dependencyMap)) {
                $deps = $this->dependencyMap[$moduleName];
                foreach ($deps as $dep) {
                    if (!in_array($dep, $tree, true)) {
                        $this->collectDependencyTree($dep, $tree);
                    }
                }
            }
        } elseif (is_array($modules)) {
            foreach ($modules as $moduleName) {
                $this->collectDependencyTree($moduleName, $tree);
            }
        }
    }
}