<?php


namespace KamilleNaiveImporter\InstallSummary;


use KamilleNaiveImporter\ImportSummary\ImportSummaryInterface;

interface InstallSummaryInterface extends ImportSummaryInterface
{
    /**
     * Fresh installed modules
     */
    public function getNewlyInstalledModules();

    public function getAlreadyInstalledModules();

    /**
     * return modules left uninstalled after a call to an erroneous/buggy install command
     */
    public function getUninstalledModules();

    /**
     * return modules uninstalled via the uninstall command.
     * If a module was already uninstalled, it's ignored.
     */
    public function getSuccessfullyUninstalledModules();
}