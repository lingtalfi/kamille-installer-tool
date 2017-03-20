<?php


namespace ProgramPrinter;


class ProgramPrinter implements ProgramPrinterInterface
{
    public static function create()
    {
        return new static();
    }


    public function error($msg, $br = true)
    {
        echo "\e[31m$msg\e[0m";
        if (true === $br) {
            echo PHP_EOL;
        }
    }

    public function success($msg, $br = true)
    {
        echo "\e[32m$msg\e[0m";
        if (true === $br) {
            echo PHP_EOL;
        }
    }

    public function info($msg, $br = true)
    {
        echo "\e[34m$msg\e[0m";
        if (true === $br) {
            echo PHP_EOL;
        }
    }

    public function warn($msg, $br = true)
    {
        // 1;31: light red
        // 1;33: yellow
        echo "\e[31m$msg\e[0m";
        if (true === $br) {
            echo PHP_EOL;
        }
    }

    public function help()
    {
        echo <<<HELP
\e[34m        
Usage
-------
kamille import {module} {importerId}?                    # import one module and dependencies, skip already existing module(s)/dependencies
kamille import -f {module} {importerId}?                 # import one module and dependencies, replace already existing module(s)/dependencies
kamille install {module} {importerId}?                   # call the install method of the given module (it fails if the module is not imported already)
kamille install -f {module} {importerId}?                # import and install one module and all its dependencies 
kamille uninstall {module} {importerId}?                 # call the uninstall method of the given module 
kamille list {importerId}?                               # list available modules
kamille listimported                                     # list imported modules
kamille listinstalled                                    # list installed modules
kamille setmodulesrelpath                                # set the relative path to the modules directory (from the app directory)
kamille getmodulesrelpath                                # get the relative path to the modules directory (from the app directory)
kamille clean                                            # removes the .git, .gitignore, .idea and .DS_Store files at the top level of your application's directory

For instance: 
    kamille import Connexion
    kamille import Connexion KamilleWidgets
    kamille import -f Connexion 
    kamille import -f Connexion KamilleWidgets
    kamille install Connexion 
    kamille install Connexion KamilleWidgets 
    kamille install -f Connexion 
    kamille install -f Connexion KamilleWidgets
    kamille uninstall Connexion 
    kamille uninstall Connexion KamilleWidgets
    kamille list 
    kamille list KamilleWidgets
    kamille listimported 
    kamille listinstalled                      
    kamille setmodulesrelpath
    kamille getmodulesrelpath
    kamille clean
    
    
Options
-------------
-f: when used with the import keyword, force overwriting of existing modules and dependencies. If not set, the Importer will skip existing planets/dependencies.
    when used with the install keyword, force the importing (in force mode too) of the modules
    

\e[0m
HELP;
    }


    public function say($msg, $br = true)
    {
        echo $msg;
        if (true === $br) {
            echo PHP_EOL;
        }
    }

}