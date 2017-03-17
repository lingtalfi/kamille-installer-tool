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
uni import {planetName} {universe}?                 # import one planet and dependencies, skip already existing planet(s)/dependencies
uni import -f {planetName} {universe}?              # import one planet and dependencies, replace already existing planet(s)/dependencies
uni import -f _all_ {universe}?                      # import all planets and dependencies, replace already existing planet(s)/dependencies
uni list {universe}?                                # list available planets
uni setlocaluniverse {localUniversePath}            # set the local universe path
uni getlocaluniverse                                # get the local universe path
uni tosymlink                                       # converts the planets of the application to symlinks (to the local universe)
uni todir                                           # converts the planets of the application to directories (copied from the local universe)
uni clean                                           # removes the .git, .gitignore, .idea and .DS_Store files at the top level of your application's planet directories

For instance: 
    uni import Bat
    uni import Bat ling
    uni import -f Bat
    uni import -f _all_
    uni import -f _all_ ling
    uni list
    uni list ling
    uni setlocaluniverse /myphp/universe
    uni getlocaluniverse
    uni tosymlink
    uni todir
    uni clean
    
    
Options
-------------
-f: force overwriting of existing planets and dependencies. If not set, the Importer will skip existing planets/dependencies.    
    

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