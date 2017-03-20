<?php


namespace KamilleNaiveImporter\ImportSummary;

/**
 * This object summarizes the result of the import operation.
 *
 * You get:
 * - statistical info
 * - error messages in case things went bad
 *
 */
interface ImportSummaryInterface
{

    public function isSuccessful();

    /**
     * Modules which have actually been replaced (overwritten)
     */
    public function getReimportedModules();

    public function getAlreadyImportedModules();

    public function getNotImportedModules();


    public function getErrorMessages();


    public function setReimportedModules(array $modules);

    public function setAlreadyImportedModules(array $modules);

    public function setNotImportedModules(array $modules);


    public function setErrorMessages(array $errorMessages);
}