<?php


namespace KamilleNaiveImporter\Importer;


use KamilleNaiveImporter\KamilleNaiveImporter;

interface KamilleNaiveImporterAwareImporterInterface
{

    public function setKamilleNaiveImporter(KamilleNaiveImporter $uni);
}