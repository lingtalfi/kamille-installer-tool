<?php


namespace KamilleNaiveImporter\Importer;


abstract class AbstractImporter implements ImporterInterface
{


    private $aliases;

    public function __construct()
    {
        $this->aliases = [];
    }

    public static function create()
    {
        return new static();
    }

    public function getFullName()
    {
        return get_called_class();
    }

    public function getAliases()
    {
        return $this->aliases;
    }

    public function setAliases(array $aliases)
    {
        $this->aliases = $aliases;
        return $this;
    }


}