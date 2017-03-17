<?php


namespace KamilleNaiveImporter;


use Kamille\Architecture\ModuleInstaller\ModuleInstaller;
use Kamille\Architecture\ModuleInstaller\ModuleInstallerInterface;
use KamilleNaiveImporter\Importer\KamilleImporterInterface;
use ProgramPrinter\ProgramPrinter;
use ProgramPrinter\ProgramPrinterInterface;
use KamilleNaiveImporter\Exception\KamilleNaiveImporterException;
use KamilleNaiveImporter\ImportSummary\ImportSummary;
use KamilleNaiveImporter\ImportSummary\ImportSummaryInterface;

/**
 * This is the naive importer for the kamille framework.
 * It is naive because it doesn't try to deal with version numbers (when importing a module),
 * it just uses the latest version available.
 *
 *
 * What you can do with this class is:
 *
 *
 * - import module(s)
 * - list installed/imported modules
 *
 */
class KamilleNaiveImporter
{


    private $appDir;
    private $modulesRelativePath;

    /**
     * The value of the force flag.
     * By default, it's false/
     *
     */
    private $_forceImport;
    private $printer;

    /**
     * @var ModuleInstallerInterface
     */
    private $moduleInstaller;


    /**
     * @var KamilleImporterInterface[]
     */
    private $importers;


    public function __construct()
    {
        $this->modulesRelativePath = "class-modules";
        $this->importers = [];
        $this->_forceImport = false;
    }

    public static function create()
    {
        return new static();
    }

    public function setAppDir($appDir)
    {
        $this->appDir = $appDir;
        return $this;
    }

    public function setModulesRelativePath($modulesRelativePath)
    {
        $this->modulesRelativePath = $modulesRelativePath;
        return $this;
    }

    public function setProgramPrinter(ProgramPrinterInterface $printer)
    {
        $this->printer = $printer;
        return $this;
    }


    public function addImporter(KamilleImporterInterface $importer)
    {
        $this->importers[] = $importer;
        return $this;
    }

    public function forceImport($forceImport)
    {
        $this->_forceImport = $forceImport;
        return $this;
    }


    /**
     * Import a module and its dependencies.
     * If the force flag is set to true, then it replaces already existing imported modules.
     * Otherwise, it skips them.
     *
     *
     * @param $moduleName
     * @param null $importerId
     * @return ImportSummary|ImportSummaryInterface|static
     * @throws KamilleNaiveImporterException
     */
    public function import($moduleName, $importerId = null)
    {
        if (null === $this->appDir) {
            throw new KamilleNaiveImporterException("appDir not set");
        }

        $modulesDir = $this->appDir . "/" . $this->modulesRelativePath;

        foreach ($this->importers as $importer) {
            if (
                null === $importerId ||
                (null !== $importerId && $importerId === $importer->getImporterId())
            ) {
                if (true === $importer->canImport($moduleName)) {
                    $summary = $importer->import($modulesDir, $moduleName, $this->_forceImport);
                    return $summary;
                }
            }
        }
        $summary = ImportSummary::create();
        $summary->setSuccessful(false);
        $summary->setUninstalledModules([$moduleName]);
        return $summary;
    }


    public function install($moduleName, $importerId = null)
    {
        if (null === $this->appDir) {
            throw new KamilleNaiveImporterException("appDir not set");
        }
        $force = $this->_forceImport;
        if (false === $force) {
            $listImported = $this->getAvailableModules($importerId);

            if (in_array($moduleName, $listImported, true)) {
                $moduleInstaller = $this->getModuleInstaller();
                return $moduleInstaller->install($moduleName);
            } else {
                throw new KamilleNaiveImporterException("This module is not imported: $moduleName. Please import it first (or use the -f flag with the install command)");
            }
        } else {
            $summary = $this->import($moduleName, $importerId);
            if(true===$summary->isSuccessful()){

            }
        }


        $modulesDir = $this->appDir . "/" . $this->modulesRelativePath;

        foreach ($this->importers as $importer) {
            if (
                null === $importerId ||
                (null !== $importerId && $importerId === $importer->getImporterId())
            ) {
                if (true === $importer->canImport($moduleName)) {
                    $summary = $importer->import($modulesDir, $moduleName, $this->_forceImport);
                    return $summary;
                }
            }
        }
        $summary = ImportSummary::create();
        $summary->setSuccessful(false);
        $summary->setUninstalledModules([$moduleName]);
        return $summary;
    }


    public function listAvailableModules($importerId = null)
    {
        $printer = $this->getPrinter();
        $modules = $this->getAvailableModules($importerId);
        if (count($modules) > 0) {
            echo $printer->info("Importer " . $importerId . ":");
            foreach ($modules as $moduleName) {
                echo "- " . $moduleName . PHP_EOL;
            }
        }
    }

    public function listImportedModules()
    {
        $printer = $this->getPrinter();
        $modulesDir = $this->appDir . "/" . $this->modulesRelativePath;
        $list = [];
        if (!is_dir($modulesDir)) {
            @mkdir($modulesDir, 0777, true);
        }
        if (is_dir($modulesDir)) {
            $files = scandir($modulesDir);
            foreach ($files as $f) {
                $file = $modulesDir . "/" . $f;
                if ('.' !== $f && '..' !== $f) {
                    if (is_dir($file)) {
                        $list[] = $f;
                    }
                }
            }
        } else {
            throw new KamilleNaiveImporterException("Could not create the modulesDir: $modulesDir");
        }

        foreach ($list as $moduleName) {
            $printer->say("- $moduleName");
        }
    }


    public function listInstalledModules()
    {
        $printer = $this->getPrinter();
        // assuming the application uses XModuleInstaller as the module installer
        $f = $this->appDir . "/modules.txt";
        $ret = [];
        if (file_exists($f)) {
            $ret = file($f, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $ret = array_filter($ret);
        }

        foreach ($ret as $moduleName) {
            $printer->say("- $moduleName");
        }
    }








    //--------------------------------------------
    //
    //--------------------------------------------
    protected function getAvailableModules($importerId = null)
    {
        $modules = [];
        foreach ($this->importers as $importer) {
            if (
                null === $importerId ||
                (null !== $importerId && $importerId === $importer->getImporterId())
            ) {
                $modules = $importer->listAvailableModules();
            }
        }
        return $modules;
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    /**
     * @return ProgramPrinterInterface
     */
    private function getPrinter()
    {
        if (null === $this->printer) {
            $this->printer = new ProgramPrinter();
        }
        return $this->printer;
    }

    /**
     * @return ModuleInstallerInterface
     */
    private function getModuleInstaller()
    {
        if (null === $this->moduleInstaller) {
            $this->moduleInstaller = new ModuleInstaller();
            $this->moduleInstaller->setAppDir($this->appDir);
        }
        return $this->moduleInstaller;
    }
}