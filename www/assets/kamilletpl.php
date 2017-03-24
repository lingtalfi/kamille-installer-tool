#!/usr/bin/env php
<?php


//replace


//--------------------------------------------
// BELOW THIS LINE, EDIT MANUALLY
/**
 * Above this line, you can simply call the pack.php script
 * (http://kit/pack.php in my case)
 * to create the packed string
 */
//--------------------------------------------

namespace {


    use BumbleBee\Autoload\ButineurAutoloader;
    use KamilleNaiveImporter\Importer\KamilleModulesImporter;
    use KamilleNaiveImporter\KamilleNaiveImporter;
    use KamilleNaiveImporter\Log\ProgramLog;
    use Output\ProgramOutput;
    use Tools\CleanerTool;


    $appDir = getcwd();


    //--------------------------------------------
    // AUTOLOAD CODE
    //--------------------------------------------
    $_SERVER['APPLICATION_ENVIRONMENT'] = "dev"; // hack environment here depending on your prefs
    $file = $appDir . '/boot.php';
    if (file_exists($file)) {
        require_once $file;

        /**
         * Uncomment the code below in test mode (that should be me only),
         * and then delete the "packed" code above the "BELOW THIS LINE, EDIT MANUALLY".
         */
//        ButineurAutoloader::getInst()
//            ->addLocation(__DIR__ . "/pprivate");
    }

    $output = ProgramOutput::create();


    function getHelpText()
    {
        return <<<HELP
\e[34m        
Usage
-------
kamille import {module}                     # import a module and its dependencies, skip already existing module(s)/dependencies
kamille import -f {module}                  # import a module and its dependencies, replace already existing module(s)/dependencies
kamille install {module}                    # install a module and its dependencies, will import if necessary, skip already existing module(s)/dependencies
kamille install -f {module}                 # install a module and its dependencies, will import if necessary, replace already existing module(s)/dependencies 
kamille uninstall {module}                  # call the uninstall method of the given module 
kamille list {importerAlias}?               # list available modules
kamille listd {importerAlias}?              # list available modules with their description if any
kamille listimported                        # list imported modules
kamille listinstalled                       # list installed modules
kamille search {importerAlias}?             # search through available modules names
kamille searchd {importerAlias}?            # search through available modules names and/or description
kamille clean                               # removes the .git, .gitignore, .idea and .DS_Store files at the top level of your modules' directories
kamille cleanr                              # removes the .git, .gitignore, .idea and .DS_Store files in your modules directories, recursively 

For instance: 
    kamille import Connexion
    kamille import km.Connexion 
    kamille import -f Connexion 
    kamille import -f km.Connexion 
    kamille install Connexion 
    kamille install km.Connexion  
    kamille install -f Connexion 
    kamille install -f km.Connexion 
    kamille uninstall Connexion 
    kamille uninstall km.Connexion
    kamille list 
    kamille list km
    kamille listd 
    kamille listd km
    kamille listimported 
    kamille listinstalled    
    kamille search ling     
    kamille search ling km    
    kamille searchd kaminos
    kamille searchd kaminos km
    kamille clean
    kamille cleanr
    
    
Options
-------------
-f: when used with the import keyword, force overwriting of existing modules and dependencies. If not set, the Importer will skip existing planets/dependencies.
    when used with the install keyword, force the importing (in force mode too) of the modules
    

\e[0m
HELP;

    }


    function indent($text)
    {
        $nbSpaces = 4;
        $p = explode(PHP_EOL, $text);
        $sp = str_repeat(" ", $nbSpaces);
        return $sp . implode(PHP_EOL . $sp, $p);
    }

    try {

        $modulesRelativePath = 'class-modules';

        $force = false;
        if (array_key_exists(2, $argv) && '-f' === $argv[2]) {
            $force = true;
            unset($argv[2]);
            $argv = array_merge($argv);
        }

        // verbose?
        $verbose = false;
        foreach ($argv as $k => $arg) {
            if ('-v' === $arg) {
                $verbose = true;
                unset($argv[$k]);
                $argv = array_merge($argv);
            }
        }


//        $verbose = true;
        if (false === $verbose) {
            $output->setDampened(["debug"]);
        }


        $kamille = KamilleNaiveImporter::create()
            ->setOutput($output)
            ->setAppDir($appDir)
            ->addImporter(KamilleModulesImporter::create()->setAliases(['km']));


        //--------------------------------------------
        // IMPORT
        // INSTALL
        // UNINSTALL
        //--------------------------------------------
        if (array_key_exists(1, $argv) &&
            ('import' === $argv[1] || 'install' === $argv[1] || 'uninstall' === $argv[1])
            && array_key_exists(2, $argv)
        ) {


            $command = $argv[1];
            $moduleName = $argv[2];


            $modulesDir = $appDir . "/" . $modulesRelativePath;
            if (false === file_exists($modulesDir)) {
                @mkdir($modulesDir, 0777, true);
            }

            if (file_exists($modulesDir)) {


                if ('import' === $command) {
                    $kamille->import($moduleName, $modulesDir, $force);
                } elseif ('install' === $command) {
                    $kamille->install($moduleName, $modulesDir, $force);
                } elseif ('uninstall' === $command) {
                    $kamille->uninstall($moduleName, $modulesDir);
                }
            } else {
                ProgramLog::error("Cannot create the modules directory: $modulesDir");
            }
        }
        //--------------------------------------------
        // LIST
        //--------------------------------------------
        elseif (array_key_exists(1, $argv) &&
            ('list' === $argv[1] || 'listd' === $argv[1])
        ) {

            $useDescription = ('listd' === $argv[1]);

            $importerAlias = null;
            if (array_key_exists(2, $argv)) {
                $importerAlias = $argv[2];
            }


            $availableModules = $kamille->getAvailableModulesList($importerAlias);
            if (count($availableModules) > 0) {
                foreach ($availableModules as $importerFullName => $modules) {
                    $output->notice("Modules available for importer $importerFullName:");
                    $output->notice("-------------------------------------------------");
                    foreach ($modules as $moduleInfo) {
                        if (false === $useDescription) {
                            $output->notice("- " . $moduleInfo[0]);
                        } else {
                            $output->info("- " . $moduleInfo[0]);
                            $output->notice(indent($moduleInfo[1]));
                        }
                    }
                }
            }

        } elseif (array_key_exists(1, $argv) && 'listimported' === $argv[1]) {
            $modules = $kamille->getImportedModulesList();
            foreach ($modules as $module) {
                $output->notice("- $module");
            }
        } elseif (array_key_exists(1, $argv) && 'listinstalled' === $argv[1]) {
            $modules = $kamille->getInstalledModulesList();
            foreach ($modules as $module) {
                $output->notice("- $module");
            }
        }
        //--------------------------------------------
        // SEARCH
        //--------------------------------------------
        elseif (
            array_key_exists(1, $argv) &&
            ('search' === $argv[1] || 'searchd' === $argv[1]) &&
            array_key_exists(2, $argv)
        ) {

            $useDescription = ('searchd' === $argv[1]);

            function highlight($text, $search)
            {
                $ret = $text;
                $positions = [];


                $offset = 0;
                $len = mb_strlen($search);
                while (false !== ($pos = mb_stripos($text, $search, $offset))) {
                    $positions[] = $pos;
                    $offset = $pos + 1;
                }
                rsort($positions);

                if (count($positions) > 0) {
                    foreach ($positions as $pos) {
                        $s = "";
                        $s .= mb_substr($ret, 0, $pos);
                        $s .= "\033[1;37m\033[44m" . mb_substr($text, $pos, $len) . "\033[0m";
//                        $s .= "[" . mb_substr($ret, $pos, $len) . "]";
                        $s .= mb_substr($ret, $pos + $len);
                        $ret = $s;
                    }


                }
                return $ret;
            }

            $importerAlias = null;
            if (array_key_exists(3, $argv)) {
                $importerAlias = $argv[3];
            }
            $search = strtolower(trim($argv[2]));
            $im2modules = $kamille->search($search, $useDescription, $importerAlias);

            foreach ($im2modules as $importerFullName => $modules) {
                $output->notice("importer $importerFullName:");
                foreach ($modules as $moduleInfo) {

                    $module = highlight($moduleInfo[0], $search);
                    $description = highlight($moduleInfo[1], $search);

                    if (false === $useDescription) {
                        $output->notice("- " . $module);
                    } else {
                        $output->info("- " . $module);
                        $output->notice(indent($description));
                    }
                }
            }
        }
        //--------------------------------------------
        // CLEAN
        //--------------------------------------------
        elseif (array_key_exists(1, $argv) &&
            (
                'clean' === $argv[1] ||
                'cleanr' === $argv[1]
            )
        ) {

            $recursive = ('cleanr' === $argv[1]);
            $appDir = getcwd();

            if (array_key_exists(2, $argv)) {
                $targetDir = $argv[2];
            } else {
                $targetDir = $appDir . "/" . $modulesRelativePath;
            }

            if (is_dir($targetDir)) {
                CleanerTool::create()->clean($targetDir, $recursive);

                $output->success('The following directory has been successfully cleaned: ' . $targetDir . '');
            } else {
                $output->error("The application modules directory doesn't exist. Please create it first, then re-execute this command. Expected path: $targetDir");
            }
        } else {
            $output->notice("");
            $output->error("Invalid arguments");
            $output->notice(getHelpText());
        }

    } catch (\Exception $e) {
        $output->error($e->getMessage());
    }
}



