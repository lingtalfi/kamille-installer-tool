<?php


namespace KamilleNaiveImporter;


use Kamille\Utils\Exception\UserErrorException;
use Kamille\Utils\ModuleInstaller\ModuleInstaller;
use Kamille\Utils\ModuleInstaller\ModuleInstallerInterface;
use Kamille\Utils\StepTracker\ConsoleStepTracker;
use Kamille\Utils\StepTracker\StepTrackerAwareInterface;
use KamilleNaiveImporter\Importer\KamilleImporterInterface;
use KamilleNaiveImporter\InstallSummary\InstallSummary;
use KamilleNaiveImporter\InstallSummary\InstallSummaryInterface;
use ProgramPrinter\ProgramPrinter;
use ProgramPrinter\ProgramPrinterInterface;
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
     * @return ImportSummaryInterface
     * @throws UserErrorException
     */
    public function import($moduleName, $importerId = null)
    {
        if (null === $this->appDir) {
            throw new UserErrorException("appDir not set");
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
            throw new UserErrorException("appDir not set");
        }
        $force = $this->_forceImport;
        if (false === $force) {
            $availableModules = $this->getAvailableModules($importerId);
            if (in_array($moduleName, $availableModules, true)) {
                $listImported = $this->getImportedModules();
                if (in_array($moduleName, $listImported, true)) {
                    $summary = $this->installModule($moduleName);
                } else {
                    throw new UserErrorException("This module is not imported: $moduleName. Please import it first (or use the -f flag with the install command)");
                }
            } else {
                throw new UserErrorException("This module is not available with importer $importerId");
            }
        } else {
            $importSummary = $this->import($moduleName, $importerId);
            if (true === $importSummary->isSuccessful()) {
                $summaries = [];
                $list = $importSummary->getReimportedModules();
                foreach ($list as $item) {
                    $summaries[] = $this->installModule($item);
                }
                $summary = $this->mergeInstallSummaries($summaries);
                $summary = $this->mergeImportSummaryIntoInstallSummary($importSummary, $summary);
            } else {
                $summary = InstallSummary::create();
                $summary->setSuccessful(false);
                $summary->setNotImportedModules([$moduleName]);
                $summary->addUninstalledModule($moduleName);
            }
        }
        return $summary;
    }


    public function uninstall($moduleName, $importerId = null)
    {
        if (null === $this->appDir) {
            throw new UserErrorException("appDir not set");
        }

        $availableModules = $this->getAvailableModules($importerId);
        if (in_array($moduleName, $availableModules, true)) {
            $listImported = $this->getImportedModules();
            if (in_array($moduleName, $listImported, true)) {
                $summary = $this->uninstallModule($moduleName, $importerId);
            } else {
                throw new UserErrorException("This module is not imported: $moduleName. Therefore it cannot be uninstalled");
            }
        } else {
            throw new UserErrorException("This module is not available with importer $importerId");
        }
        return $summary;
    }


    public function listAvailableModules($importerId = null)
    {
        $printer = $this->getPrinter();
        $modules = $this->getAvailableModulesByImporter($importerId);
        if (count($modules) > 0) {
            foreach ($modules as $importerId => $importerModules) {
                echo $printer->info("Importer " . $importerId . ":");
                foreach ($importerModules as $moduleName) {
                    echo "- " . $moduleName . PHP_EOL;
                }
            }
        }
    }

    public function listImportedModules()
    {
        $printer = $this->getPrinter();
        $modulesDir = $this->appDir . "/" . $this->modulesRelativePath;
        if (!is_dir($modulesDir)) {
            @mkdir($modulesDir, 0777, true);
        }
        $list = $this->getImportedModules();
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
                $modules = array_merge($modules, $importer->listAvailableModules());

            }
        }
        $modules = array_unique($modules);
        return $modules;
    }

    protected function getAvailableModulesByImporter($importerId = null)
    {
        $modules = [];
        foreach ($this->importers as $importer) {
            if (
                null === $importerId ||
                (null !== $importerId && $importerId === $importer->getImporterId())
            ) {

                $id = $importer->getImporterId();
                if (!array_key_exists($id, $modules)) {
                    $modules[$id] = [];
                }
                $modules[$id] = array_merge($modules[$id], $importer->listAvailableModules());
            }
        }
        return $modules;
    }

    //--------------------------------------------
    //
    //--------------------------------------------

    private function getImportedModules()
    {
        $modulesDir = $this->appDir . "/" . $this->modulesRelativePath;
        $list = [];
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
        }
        return $list;
    }


    private function mergeImportSummaryIntoInstallSummary(ImportSummaryInterface $importSummary, InstallSummaryInterface $installSummary)
    {

        $alreadyImportedModules = array_merge($importSummary->getAlreadyImportedModules(), $installSummary->getAlreadyImportedModules());
        $notImportedModules = array_merge($importSummary->getNotImportedModules(), $installSummary->getNotImportedModules());
        $reimportedModules = array_merge($importSummary->getReimportedModules(), $installSummary->getReimportedModules());
        $errorMessages = array_merge($importSummary->getErrorMessages(), $installSummary->getErrorMessages());

        $alreadyImportedModules = array_unique($alreadyImportedModules);
        $notImportedModules = array_unique($notImportedModules);
        $reimportedModules = array_unique($reimportedModules);
        $errorMessages = array_unique($errorMessages);


        $installSummary->setAlreadyImportedModules($alreadyImportedModules);
        $installSummary->setReimportedModules($reimportedModules);
        $installSummary->setNotImportedModules($notImportedModules);
        $installSummary->setErrorMessages($errorMessages);
        return $installSummary;
    }


    private function mergeInstallSummaries(array $summaries)
    {

        $errorMessages = [];
        $alreadyImportedModules = [];
        $notImportedModules = [];
        $reimportedModules = [];
        $alreadyInstalledModules = [];
        $newlyInstalledModules = [];
        $uninstalledModules = [];
        $successfullyUninstalledModules = [];
        $successful = true;

        foreach ($summaries as $summary) {
            /**
             * @var InstallSummaryInterface $summary
             */
            $errorMessages = array_merge($errorMessages, $summary->getErrorMessages());
            $alreadyImportedModules = array_merge($alreadyImportedModules, $summary->getAlreadyImportedModules());
            $notImportedModules = array_merge($notImportedModules, $summary->getNotImportedModules());
            $reimportedModules = array_merge($reimportedModules, $summary->getReimportedModules());
            $alreadyInstalledModules = array_merge($alreadyInstalledModules, $summary->getAlreadyInstalledModules());
            $newlyInstalledModules = array_merge($newlyInstalledModules, $summary->getNewlyInstalledModules());
            $uninstalledModules = array_merge($uninstalledModules, $summary->getUninstalledModules());
            $successfullyUninstalledModules = array_merge($successfullyUninstalledModules, $summary->getSuccessfullyUninstalledModules());

            if (false === $summary->isSuccessful()) {
                $successful = false;
            }
        }


        $errorMessages = array_unique($errorMessages);
        $alreadyImportedModules = array_unique($alreadyImportedModules);
        $notImportedModules = array_unique($notImportedModules);
        $reimportedModules = array_unique($reimportedModules);
        $alreadyInstalledModules = array_unique($alreadyInstalledModules);
        $newlyInstalledModules = array_unique($newlyInstalledModules);
        $uninstalledModules = array_unique($uninstalledModules);
        $successfullyUninstalledModules = array_unique($successfullyUninstalledModules);

        return InstallSummary::create()
            ->setErrorMessages($errorMessages)
            ->setSuccessfullyUninstalledModules($successfullyUninstalledModules)
            ->setUninstalledModules($uninstalledModules)
            ->setNewlyInstalledModules($newlyInstalledModules)
            ->setAlreadyInstalledModules($alreadyInstalledModules)
            ->setAlreadyImportedModules($alreadyImportedModules)
            ->setNotImportedModules($notImportedModules)
            ->setReimportedModules($reimportedModules)
            ->setSuccessful($successful);

    }


    private function installModule($moduleName)
    {



        $moduleInstaller = $this->getModuleInstaller();
        if ($moduleInstaller instanceof StepTrackerAwareInterface) {
            $moduleInstaller->setStepTracker($this->getStepTracker());
        }
        //
        $isSuccessful = false;
        $alreadyInstalled = false;
        $errorMessage = null;
        if (false === $moduleInstaller->isInstalled($moduleName)) {
            try {
                $this->getPrinter()->info("Installing module $moduleName");
                if (true === $moduleInstaller->install($moduleName)) {
                    $isSuccessful = true;
                }
            } catch (\Exception $e) {
                $isSuccessful = false;
                $errorMessage = $e->getMessage();
            }

        } else {
            $isSuccessful = true;
            $alreadyInstalled = true;
        }


        $summary = InstallSummary::create();
        $summary->setSuccessful($isSuccessful);
        if (true === $isSuccessful) {
            if (true === $alreadyInstalled) {
                $summary->addAlreadyInstalledModule($moduleName);
            } else {
                $summary->addNewlyInstalledModule($moduleName);
            }
        } else {
            $summary->addUninstalledModule($moduleName);
            if (null !== $errorMessage) {
                $summary->addErrorMessage($errorMessage);
            }
        }
        //
        return $summary;
    }


    private function uninstallModule($moduleName)
    {

        $moduleInstaller = $this->getModuleInstaller();
        if ($moduleInstaller instanceof StepTrackerAwareInterface) {
            $moduleInstaller->setStepTracker($this->getStepTracker());
        }
        //
        $isSuccessful = false;
        $successfullyUninstalled = false;
        $errorMessage = null;
        if (true === $moduleInstaller->isInstalled($moduleName)) {
            try {
                $this->getPrinter()->info("Uninstalling module $moduleName");
                if (true === $moduleInstaller->uninstall($moduleName)) {
                    $successfullyUninstalled = true;
                    $isSuccessful = true;
                }
            } catch (\Exception $e) {
                $isSuccessful = false;
                $errorMessage = $e->getMessage();
            }

        } else {
            $isSuccessful = true;
        }


        $summary = InstallSummary::create();
        $summary->setSuccessful($isSuccessful);
        if (true === $isSuccessful) {
            if (true === $successfullyUninstalled) {
                $summary->addSuccessfullyUninstalledModule($moduleName);
            }
        } else {
            if (null !== $errorMessage) {
                $summary->addErrorMessage($errorMessage);
            }
        }
        //
        return $summary;
    }


    private function getStepTracker()
    {
        return ConsoleStepTracker::create();
    }

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