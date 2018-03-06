<?php


namespace KamilleProgram\Program;


use ApplicationItemManager\ApplicationItemManagerInterface;
use ApplicationItemManager\Exception\ApplicationItemManagerException;
use ApplicationItemManager\Program\ApplicationItemManagerProgram;
use Bat\FileSystemTool;
use CommandLineInput\CommandLineInputInterface;
use Dir2Symlink\ProgramOutputAwareDir2Symlink;
use DirectoryCleaner\DirectoryCleaner;
use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
use Output\ProgramOutputInterface;
use Program\ProgramHelper;
use Program\ProgramInterface;

class KamilleApplicationItemManagerProgram extends ApplicationItemManagerProgram
{

    /**
     * @var ApplicationItemManagerInterface
     */
    private $widgetManager;
    private $widgetsImportDirectory;
    private $currentDir;

    public function __construct()
    {
        parent::__construct();

        $itemType = $this->getItemType();

        $this
            //--------------------------------------------
            // KAMILLE APPS
            //--------------------------------------------
            ->addCommand("newapp", function (CommandLineInputInterface $input, ProgramOutputInterface $output, ProgramInterface $program) use ($itemType) {

                $appName = $input->getParameter(2);
                $inPlace = $input->getFlagValue("in-place");
                $curDir = $this->getCurrentDir();

                $executeCommand = false;
                if (true === $inPlace) {
                    $appName = '.';
                    /**
                     * The inplace command option won't work if the directory is not empty,
                     * and mac likes to create those "useless" .DS_Store a lot, so...
                     */
                    if (file_exists($curDir . "/.DS_Store")) {
                        unlink($curDir . "/.DS_Store");
                    }

                    $executeCommand = true;
                } else {
                    if (null === $appName) {
                        $appName = "kamille-app";
                    }
                    $kamilleAppDir = $curDir . "/" . $appName;

                    if (is_dir($kamilleAppDir)) {
                        $output->warn("The directory already exists, aborting ($kamilleAppDir).");
                    } else {
                        $executeCommand = true;
                    }
                }


                if (true === $executeCommand) {

                    $cmd = 'git clone https://github.com/lingtalfi/kamille-app.git ' . $appName;
                    $output->info("Creating kamille app, using command: $cmd");
                    passthru($cmd);
                }
            })
            //--------------------------------------------
            //
            //--------------------------------------------
            ->addCommand("wimport", function (CommandLineInputInterface $input, ProgramOutputInterface $output, ProgramInterface $program) use ($itemType) {
                $force = $input->getFlagValue('f');
                if (false !== ($itemName = ProgramHelper::getParameter(2, $itemType, $input, $output))) {
                    $this->widgetManager->import($itemName, $force);
                }
            })
            ->addCommand("wimportall", function (CommandLineInputInterface $input, ProgramOutputInterface $output, ProgramInterface $program) use ($itemType) {
                $force = $input->getFlagValue('f');
                $repoId = $input->getParameter(2);
                if (true === $this->widgetManager->importAll($repoId, $force)) {
                    $output->success("All items were imported");
                } else {
                    $output->error("Some items couldn't be imported");
                }
            })
            ->addCommand("winstall", function (CommandLineInputInterface $input, ProgramOutputInterface $output, ProgramInterface $program) use ($itemType) {
                $force = $input->getFlagValue('f');
                if (false !== ($itemName = ProgramHelper::getParameter(2, $itemType, $input, $output))) {
                    $this->widgetManager->install($itemName, $force);
                }
            })
            ->addCommand("winstallall", function (CommandLineInputInterface $input, ProgramOutputInterface $output, ProgramInterface $program) use ($itemType) {
                $force = $input->getFlagValue('f');
                $repoId = $input->getParameter(2);
                if (true === $this->widgetManager->installAll($repoId, $force)) {
                    $output->success("All items were installed");
                } else {
                    $output->error("Some items couldn't be installed");
                }
            })
            ->addCommand("wuninstall", function (CommandLineInputInterface $input, ProgramOutputInterface $output, ProgramInterface $program) use ($itemType) {
                if (false !== ($itemName = ProgramHelper::getParameter(2, $itemType, $input, $output))) {
                    $this->widgetManager->uninstall($itemName);
                }
            })
            ->addCommand("wupdateall", function (CommandLineInputInterface $input, ProgramOutputInterface $output, ProgramInterface $program) use ($itemType) {
                $repoId = $input->getParameter(2);
                $this->widgetManager->updateAll($repoId);
            })
            ->addCommand("wlist", function (CommandLineInputInterface $input, ProgramOutputInterface $output, ProgramInterface $program) use ($itemType) {
                $repoId = $input->getParameter(2);
                $keys = null;
                $list = $this->widgetManager->listAvailable($repoId, $keys);
                foreach ($list as $item) {
                    $output->notice("- $item");
                }
            })
            ->addCommand("wlistd", function (CommandLineInputInterface $input, ProgramOutputInterface $output, ProgramInterface $program) use ($itemType) {
                $repoId = $input->getParameter(2);
                $keys = ['description'];
                $list = $this->widgetManager->listAvailable($repoId, $keys);
                foreach ($list as $itemId => $metas) {
                    $output->info("- $itemId:");
                    $output->notice($this->indent($metas['description']));
                }
            })
            ->addCommand("wlistimported", function (CommandLineInputInterface $input, ProgramOutputInterface $output, ProgramInterface $program) use ($itemType) {
                $list = $this->widgetManager->listImported();
                foreach ($list as $item) {
                    $output->notice("- $item");
                }
            })
            ->addCommand("wlistinstalled", function (CommandLineInputInterface $input, ProgramOutputInterface $output, ProgramInterface $program) use ($itemType) {
                $list = $this->widgetManager->listInstalled();
                foreach ($list as $item) {
                    $output->notice("- $item");
                }
            })
            ->addCommand("wsearch", function (CommandLineInputInterface $input, ProgramOutputInterface $output, ProgramInterface $program) use ($itemType) {
                $text = $input->getParameter(2);
                $repoId = $input->getParameter(3);
                $keys = null;
                $list = $this->widgetManager->search($text, $keys, $repoId);
                foreach ($list as $item) {

                    $highlighted = ProgramHelper::highlight($item, $text);
                    $output->notice("- $highlighted");
                }
            })
            ->addCommand("wsearchd", function (CommandLineInputInterface $input, ProgramOutputInterface $output, ProgramInterface $program) use ($itemType) {
                $text = $input->getParameter(2);
                $repoId = $input->getParameter(3);
                $keys = ['description'];
                $list = $this->widgetManager->search($text, $keys, $repoId);

                foreach ($list as $itemId => $metas) {
                    $highlightedItemId = ProgramHelper::highlight($itemId, $text);
                    $highlightedDescription = ProgramHelper::highlight($metas['description'], $text);
                    $output->info("- $highlightedItemId:");
                    $output->notice($this->indent($highlightedDescription));
                }
            })
            ->addCommand("wclean", function (CommandLineInputInterface $input, ProgramOutputInterface $output, ProgramInterface $program) use ($itemType) {


                $itemName = $input->getParameter(2);

                $dir = $this->getWidgetsImportDirectory();

                if (null !== $itemName) {
                    $dir .= "/$itemName";
                    if (!is_dir($dir)) {
                        throw new ApplicationItemManagerException("Not a directory: $dir");
                    }
                }
                $recursive = true;
                DirectoryCleaner::create()->setUseSymlinks(false)->clean($dir, $recursive);
                $output->notice("ok");
            })
            ->addCommand("wsetlocalrepo", function (CommandLineInputInterface $input, ProgramOutputInterface $output, ProgramInterface $program) use ($itemType) {

                $path = $input->getParameter(2);
                $file = $this->getWidgetsFile();
                if (true === FileSystemTool::mkfile($file, $path)) {
                    $output->notice("ok");
                } else {
                    $output->error("couldn't create the file $file");
                }
            })
            ->addCommand("wgetlocalrepo", function (CommandLineInputInterface $input, ProgramOutputInterface $output, ProgramInterface $program) use ($itemType) {
                if (false !== ($content = $this->getWidgetsLocalRepository($output))) {
                    $output->notice($content);
                }
            })
            ->addCommand("wtodir", function (CommandLineInputInterface $input, ProgramOutputInterface $output, ProgramInterface $program) use ($itemType) {
                $this->widgetsDir2Symlink("toDirectories", $output);
            })
            ->addCommand("wtolink", function (CommandLineInputInterface $input, ProgramOutputInterface $output, ProgramInterface $program) use ($itemType) {
                $this->widgetsDir2Symlink("toSymlinks", $output);
            });
    }


    public function setWidgetManager(ApplicationItemManagerInterface $manager)
    {
        $this->widgetManager = $manager;
        return $this;
    }

    public function setModuleManager(ApplicationItemManagerInterface $manager)
    {
        return parent::setManager($manager);
    }

    public function setWidgetsImportDirectory($widgetsImportDirectory)
    {
        $this->widgetsImportDirectory = $widgetsImportDirectory;
        return $this;
    }

    public function setModulesImportDirectory($modulesImportDirectory)
    {
        $this->setImportDirectory($modulesImportDirectory);
        return $this;
    }


    public function setCurrentDir($dir)
    {
        $this->currentDir = $dir;
        return $this;
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    protected function handleDebug(CommandLineInputInterface $input)
    {
        ApplicationParameters::set("debug", $input->getFlagValue("d"));
    }

    protected function getCurrentDir()
    {
        if (null === $this->currentDir) {
            $this->currentDir = getcwd();
        }
        return $this->currentDir;
    }



    //--------------------------------------------
    //
    //--------------------------------------------
    private function getWidgetsImportDirectory()
    {
        if (null !== $this->widgetsImportDirectory) {
            if (is_dir($this->widgetsImportDirectory)) {
                return $this->widgetsImportDirectory;
            } else {
                throw new ApplicationItemManagerException("widgets importDirectory not valid: " . $this->widgetsImportDirectory);
            }
        } else {
            throw new ApplicationItemManagerException("widgets importDirectory not set");
        }
    }

    private function getWidgetsFile()
    {
        if (null !== $this->widgetsImportDirectory) {
            return $this->widgetsImportDirectory . "/aimp.txt";
        } else {
            throw new ApplicationItemManagerException("widgets importDirectory not set");
        }
    }


    private function getWidgetsLocalRepository(ProgramOutputInterface $output)
    {
        $file = $this->getWidgetsFile();
        if (file_exists($file)) {
            return trim(file_get_contents($file));
        }
        $output->error("file does not exist: " . $file . ", you should probably use the wsetlocalrepo command first");
        return false;
    }

    private function widgetsDir2Symlink($method, ProgramOutputInterface $output)
    {
        $importDir = $this->getWidgetsImportDirectory();
        if (false !== ($localRepoDir = $this->getWidgetsLocalRepository($output))) {
            if (is_dir($localRepoDir)) {
                if (true === ProgramOutputAwareDir2Symlink::create()->setProgramOutput($output)->$method($localRepoDir, $importDir)) {
                    $output->notice("ok");
                } else {
                    $output->error("Couldn't convert all the entries in $importDir to directories, sorry");
                }
            } else {
                $output->error("Local repository is not a dir: $localRepoDir. Use the wsetlocalrepo command to update the value");
            }
        }
    }


}


