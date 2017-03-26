<?php


namespace Kamille\Utils\KamilleNaiveImporter\Importer;


class KamilleModulesImporter extends AbstractGithubHardcodedImporter
{

    private static $descriptions = [
        "Connexion" => <<<EEE
This module allows the user to log into the application, via a login form.
It uses the Privilege framework under the hood.
Tags: kaminos; lingtalfi
EEE
        ,
        "GentelellaWebDirectory" => <<<EEE
This module imports the gentelella admin theme into the web directory of your application.
Tags: theme; bootstrap
EEE
        ,
    ];

    protected function getGithubRepositoryName()
    {
        return "KamilleModules";
    }

    protected function getDependencyMap()
    {
        return [
            "Connexion" => ["GentelellaWebDirectory"],
            "Core" => [],
            "GentelellaWebDirectory" => [],
        ];
    }

    public function getModuleDescription($moduleName)
    {
        if (array_key_exists($moduleName, self::$descriptions)) {
            return self::$descriptions[$moduleName];
        }
        return "";
    }
}