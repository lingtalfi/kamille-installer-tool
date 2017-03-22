<?php


namespace KamilleNaiveImporter\Importer;


use KamilleNaiveImporter\Log\ProgramLog;

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
        $tree = $this->getDependencyTree($moduleName);
        foreach ($tree as $module) {
            $output = [];
            $returnVar = 0;

            $cmd = 'cd "' . $modulesDir . '"; git clone https://github.com/' . $this->githubRepoName . '/' . $module . '.git';
            exec($cmd, $output, $returnVar);

            if (0 === $returnVar) {
                ProgramLog::success("Module $module was successfully imported");
            } else {
                ProgramLog::error("An error occurred with the git clone command while importing module $module");
            }
        }
    }
}