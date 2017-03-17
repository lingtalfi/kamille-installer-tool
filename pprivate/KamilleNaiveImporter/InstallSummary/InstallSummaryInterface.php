<?php


namespace KamilleNaiveImporter\InstallSummary;


use KamilleNaiveImporter\ImportSummary\ImportSummaryInterface;

interface InstallSummaryInterface extends ImportSummaryInterface
{
    /**
     * Modules which have actually been replaced (overwritten)
     */
    public function getReinstalledModules();

    public function getAlreadyInstalledModules();

    /**
     * return modules left uninstalled after a call to an install command
     */
    public function getUninstalledModules();

    /**
     * return modules uninstalled via the uninstall command
     */
    public function getSuccessfullyUninstalled();
}