<?php


namespace KamilleNaiveImporter\ImportSummary;


interface ImportSummaryInterface
{

    public function isSuccessful();

    /**
     * Modules which have actually been replaced (overwritten)
     */
    public function getReimportedModules();

    public function getAlreadyImportedModules();

    public function getNotImportedModules();
}