<?php


namespace KamilleProgram\ApplicationItemManager;


use ApplicationItemManager\ApplicationItemManagerInterface;
use ApplicationItemManager\Importer\GithubImporter;
use ApplicationItemManager\KamilleApplicationItemManagerInterface;
use ApplicationItemManager\LingApplicationItemManager;
use KamilleProgram\Installer\KamilleModuleInstaller;
use KamilleProgram\Installer\KamilleWidgetInstaller;
use KamilleProgram\Repository\KamilleModulesRepository;
use KamilleProgram\Repository\KamilleWidgetsRepository;
use Output\ProgramOutputInterface;

class KamilleApplicationItemManager extends LingApplicationItemManager implements KamilleApplicationItemManagerInterface
{

    /**
     * @var ApplicationItemManagerInterface
     */
    private $widgetManager;


    public function prepare($appDir, ProgramOutputInterface $output)
    {

        $modulesImportDir = $appDir . "/class-modules";
        $widgetsImportDir = $appDir . "/class-widgets";



        $this->widgetManager = LingApplicationItemManager::create()
            ->setOutput($output)
            ->setInstaller(KamilleWidgetInstaller::create()->setOutput($output)->setApplicationDirectory($appDir))
            ->bindImporter('KamilleWidgets', GithubImporter::create()->setGithubRepoName("KamilleWidgets"))
            ->setFavoriteRepositoryId('KamilleWidgets')
            ->setImportDirectory($widgetsImportDir)
            ->addRepository(KamilleWidgetsRepository::create(), ["kw"]);



        $this->setOutput($output)
            ->setInstaller(KamilleModuleInstaller::create()
                ->setOutput($output)
                ->setWidgetManager($this->widgetManager)
                ->setApplicationDirectory($appDir)
            )
            ->bindImporter('KamilleModules', GithubImporter::create()->setGithubRepoName("KamilleModules"))
            ->setFavoriteRepositoryId('KamilleModules')
            ->setImportDirectory($modulesImportDir)
            ->addRepository(KamilleModulesRepository::create(), ["km"]);

    }


    //--------------------------------------------
    //
    //--------------------------------------------
    public function wimport($widget, $force = false)
    {
        return $this->widgetManager->import($widget, $force);
    }

    public function wimportAll($repoId = null, $force = false)
    {
        return $this->widgetManager->importAll($repoId, $force);
    }

    public function winstall($widget, $force = false)
    {
        return $this->widgetManager->install($widget, $force);
    }

    public function winstallAll($repoId = null, $force = false)
    {
        return $this->widgetManager->installAll($repoId, $force);
    }

    public function wuninstall($widget)
    {
        return $this->widgetManager->uninstall($widget);
    }

    public function wlistAvailable($repoId = null, array $keys = null)
    {
        return $this->widgetManager->listAvailable($repoId, $keys);
    }

    public function wlistImported()
    {
        return $this->widgetManager->listImported();
    }

    public function wlistInstalled()
    {
        return $this->widgetManager->listInstalled();
    }

    public function wsearch($text, array $keys = null, $repoId = null)
    {
        return $this->widgetManager->search($text, $keys, $repoId);
    }

}