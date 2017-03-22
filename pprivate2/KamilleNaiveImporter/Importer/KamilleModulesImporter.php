<?php


namespace KamilleNaiveImporter\Importer;


class KamilleModulesImporter extends AbstractGithubHardcodedImporter
{
    protected function getGithubRepositoryName()
    {
        return "KamilleModules";
    }

    protected function getDependencyMap()
    {
        return [
            "Connexion" => ["GentelellaWebDirectory"],
            "GentelellaWebDirectory" => [],
        ];
    }

}