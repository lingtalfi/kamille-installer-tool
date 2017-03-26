<?php


namespace Kamille\Utils\KamilleNaiveImporter\Importer;



abstract class AbstractGithubHardcodedImporter extends AbstractHardcodedImporter
{

    private $githubRepoName;



    public function __construct()
    {
        parent::__construct();
        $this->githubRepoName = $this->getGithubRepositoryName();
    }


    //--------------------------------------------
    // OVERRIDE THIS METHOD
    //--------------------------------------------
    protected function getGithubRepositoryName()
    {
        throw new \Exception("Override this method");
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    public function import($moduleName, $modulesDir)
    {
        $output = [];
        $returnVar = 0;
        $cmd = 'cd "' . $modulesDir . '"; git clone https://github.com/' . $this->githubRepoName . '/' . $moduleName . '.git';
        exec($cmd, $output, $returnVar);

        if (0 === $returnVar) {
            return true;
        } else {
            return false;
        }
    }
}