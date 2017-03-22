<?php


namespace KamilleNaiveImporter;


use Bat\FileSystemTool;
use Kamille\Module\ModuleInterface;
use KamilleNaiveImporter\Importer\ImporterInterface;
use Output\ProgramOutput;
use Output\ProgramOutputAwareInterface;
use Output\ProgramOutputInterface;


/**
 * This importer stores the installed module list in a text file at the root
 * of the target application.
 *
 */
class KamilleNaiveImporter
{
    private $importers;
    /**
     * @var ProgramOutputInterface $output
     */
    private $output;
    private $appDir;
    private $modulesDirRelPath;


    public function __construct()
    {
        $this->importers = [];
        $this->modulesDirRelPath = "class-modules";
    }


    public static function create()
    {
        return new static();
    }


    public function getInstalledModulesList()
    {
        $ret = [];
        $f = $this->getFile();
        if (file_exists($f)) {
            $ret = file($f, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $ret = array_filter($ret);
            $ret = array_unique($ret);
        }
        return $ret;
    }

    public function getImportedModulesList()
    {
        $ret = [];
        $modulesDir = $this->appDir . "/" . $this->modulesDirRelPath;
        if (is_dir($modulesDir)) {
            $files = scandir($modulesDir);
            foreach ($files as $f) {
                if ('.' !== $f && '..' !== $f) {
                    if (is_dir($modulesDir . "/" . $f)) {
                        $ret[] = $f;
                    }
                }
            }
        }
        return $ret;
    }


    /**
     * @return array, an array of module fullName => array of module names
     */
    public function getAvailableModulesList($moduleAlias = null)
    {
        $ret = [];
        if (null === $moduleAlias) {
            foreach ($this->importers as $importer) {
                /**
                 * @var ImporterInterface $importer
                 */
                $ret[$importer->getFullName()] = $importer->getAvailableModules();
            }
        } else {
            foreach ($this->importers as $importer) {
                /**
                 * @var ImporterInterface $importer
                 */
                $aliases = $importer->getAliases();
                if (in_array($moduleAlias, $aliases, true)) {
                    $ret[$importer->getFullName()] = $importer->getAvailableModules();
                    break;
                }
            }
        }
        return $ret;
    }


    public function isInstalled($moduleName)
    {
        $list = $this->getInstalledModulesList();
        return in_array($moduleName, $list, true);
    }


    public function import($moduleName, $modulesDir, $force = false)
    {
        $output = $this->getOutput();
        $hasAnError = false;
        $prefix = $this->getPrefix();
        $output->info($prefix . "Preparing import for module $moduleName");

        if (false !== ($importer = $this->getImporter($moduleName))) {

            $moduleName = $this->getCleanModuleName($moduleName);
            $importerName = $importer->getFullName();
            $output->info($prefix . "Importer $importerName has been chosen for importing module $moduleName.");

            $tree = $importer->getDependencyTree($moduleName);

            $output->info($prefix . "Creating dependency tree for module $moduleName:");
            foreach ($tree as $module) {
                $output->info("- $module");
            }


            if (true === $force) {
                $output->info($prefix . "Removing all modules (-f flag)");
                foreach ($tree as $module) {
                    $moduleDir = $modulesDir . "/" . $module;
                    FileSystemTool::remove($moduleDir);
                }
            }

            foreach ($tree as $module) {
                if (true === $importer->import($module, $modulesDir)) {
                    $output->success($prefix . "Module $module has been successfully imported");
                } else {
                    $output->error($prefix . "Failed importing module $module");
                    $hasAnError = true;
                }
            }
        } else {
            $output->error($prefix . "Cannot import module $moduleName: no importer can handle it");
            $hasAnError = true;
        }
        return (false === $hasAnError);
    }


    public function install($moduleName, $modulesDir, $force = false)
    {
        $output = $this->getOutput();
        $prefix = $this->getPrefix();
        $output->info($prefix . "Preparing install for module $moduleName");

        if (true === $this->import($moduleName, $modulesDir, $force)) {

            if (false !== ($importer = $this->getImporter($moduleName))) {
                // reinstall every module that is not installed already

                $moduleName = $this->getCleanModuleName($moduleName);


                $tree = $importer->getDependencyTree($moduleName);

                $installed = $this->getInstalledModulesList();
                foreach ($tree as $module) {
                    if (false === $force && in_array($module, $installed, true)) {
                        $output->info($prefix . "Module $module is already installed");
                        continue;
                    } else {
                        $output->info($prefix . "Installing module $module");
                        $this->installModule($module, $modulesDir);
                    }
                }
            }
        } else {
            $output->error($prefix . "The import process has failed, aborting install process for module $moduleName");
        }
    }


    public function uninstall($moduleName, $modulesDir)
    {
        $output = $this->getOutput();
        $prefix = $this->getPrefix();
        $oClass = $this->getModuleInstance($moduleName, $modulesDir);
        if ($oClass instanceof ProgramOutputAwareInterface) {
            $oClass->setProgramOutput($this->getOutput());
        }
        $oClass->uninstall();

        $output->success($prefix . "Module $moduleName has been uninstalled");
        $list = $this->getInstalledModulesList();
        unset($list[array_search($moduleName, $list)]);
        $this->writeList($list);
        return true;
    }



    //--------------------------------------------
    //
    //--------------------------------------------

    public function setAppDir($appDir)
    {
        $this->appDir = $appDir;
        return $this;
    }

    public function addImporter(ImporterInterface $importer)
    {
        $this->importers[] = $importer;
        return $this;
    }


    public function setOutput(ProgramOutputInterface $output)
    {
        $this->output = $output;
        return $this;
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    /**
     * @return ProgramOutputInterface
     */
    protected function getOutput()
    {
        if (null === $this->output) {
            $this->output = new ProgramOutput();
        }
        return $this->output;
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    /**
     * @return ModuleInterface|false
     */
    private function getModuleInstance($moduleName, $modulesDir)
    {
        $output = $this->getOutput();
        $moduleDir = $modulesDir . "/$moduleName";
        if (is_dir($moduleDir)) {
            $moduleFile = $moduleDir . "/$moduleName" . "Module.php";
            if (file_exists($moduleFile)) {
                require_once $moduleFile;
                $className = $moduleName . '\\' . $moduleName . "Module";
                $oClass = new $className();
                if ($oClass instanceof ModuleInterface) {
                    return $oClass;
                } else {
                    $output->error(sprintf("$className must be an instance of ModuleInterface, instance of %s given", get_class($oClass)));
                }
            } else {
                $output->error("module file not found: $moduleFile");
            }
        } else {
            $output->error("module not imported: $moduleName. Cannot get the module instance");
        }
        return false;
    }

    private function installModule($moduleName, $modulesDir)
    {
        $oClass = $this->getModuleInstance($moduleName, $modulesDir);
        if ($oClass instanceof ProgramOutputAwareInterface) {
            $oClass->setProgramOutput($this->getOutput());
        }
        $oClass->install();
        $list = $this->getInstalledModulesList();
        if (!in_array($moduleName, $list)) {
            $list[] = $moduleName;
        }
        $this->writeList($list);
        return true;
    }


    private function writeList(array $list)
    {
        $f = $this->getFile();
        file_put_contents($f, implode(PHP_EOL, $list));
    }

    private function getFile()
    {
        return $this->appDir . "/modules.txt";
    }


    /**
     *
     * @return ImporterInterface|false
     */
    private function getImporter($moduleName)
    {


        // uses aliases?
        $p = explode('.', $moduleName, 2);
        if (2 === count($p)) {
            $alias = $p[0];

            /**
             * @var ImporterInterface $importer
             */
            foreach ($this->importers as $importer) {
                $aliases = $importer->getAliases();
                if (in_array($alias, $aliases, true)) {
                    return $importer;
                }
            }
            // alias not recognized, we naturally call down to the next block, without interrupting...
        }


        foreach ($this->importers as $importer) {
            /**
             * @var ImporterInterface $importer
             */
            if (true === $importer->canImport($moduleName)) {
                return $importer;
            }
        }
        return false;
    }

    private function getPrefix()
    {
        return "* ";
    }

    private function getCleanModuleName($moduleName)
    {
        $p = explode('.', $moduleName);
        return array_pop($p);
    }

}