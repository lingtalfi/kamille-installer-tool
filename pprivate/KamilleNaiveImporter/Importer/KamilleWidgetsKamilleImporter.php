<?php


namespace KamilleNaiveImporter\Importer;


use Bat\FileSystemTool;
use KamilleNaiveImporter\ImportSummary\ImportSummary;
use KamilleNaiveImporter\KamilleNaiveImporter;

class KamilleWidgetsKamilleImporter implements KamilleImporterInterface
{

    private static $dependencyMap = [
        "Connexion" => ["GentelellaWebDirectory"],
        "GentelellaWebDirectory" => [],
    ];


    public static function create()
    {
        return new static();
    }


    public function getImporterId()
    {
        return "KamilleWidgets";
    }

    public function getDependencyTree($moduleName)
    {
        $tree = [];
        $this->collectDependencyTree($moduleName, $tree);
        $tree = array_unique($tree);
        return $tree;
    }


    public function listAvailableModules()
    {
        return array_keys(self::$dependencyMap);
    }


    public function canImport($moduleName)
    {
        return array_key_exists($moduleName, self::$dependencyMap);
    }

    public function import($modulesDir, $moduleName, $force = false)
    {
        $success = false;
        $summary = ImportSummary::create();
        if (is_dir($modulesDir)) {
            $success = true;


            $tree = $this->getDependencyTree($moduleName);


            foreach ($tree as $module) {
                $output = [];
                $returnVar = 0;

                $moduleDir = $modulesDir . "/" . $module;

                if (file_exists($moduleDir)) {
                    if (true === $force) {
                        FileSystemTool::remove($moduleDir);
                    } else {
                        $summary->addAlreadyImportedModule($moduleName);
                        continue;
                    }
                }
                $cmd = 'cd "' . $modulesDir . '"; git clone https://github.com/KamilleModules/' . $moduleName . '.git';
                exec($cmd, $output, $returnVar);
                if (0 === $returnVar) {
                    $summary->addReimportedModule($moduleName);
                } else {
                    $success = false;
                    $summary->addNotImportedModule($moduleName);
                }
            }
        }
        $summary->setSuccessful($success);
        return $summary;
    }




    //--------------------------------------------
    //
    //--------------------------------------------
    private function collectDependencyTree($modules, array &$tree)
    {
        if (is_string($modules)) {
            $moduleName = $modules;
            $tree[] = $moduleName;
            if (array_key_exists($moduleName, self::$dependencyMap)) {
                $deps = self::$dependencyMap[$moduleName];
                foreach ($deps as $dep) {
                    if (!in_array($dep, $tree, true)) {
                        self::collectDependencyTree($dep, $tree);
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