<?php


namespace Kamille\Services;


use Kamille\Utils\ModuleInstaller\ModuleInstaller;
use Kamille\Utils\ModuleInstaller\ModuleInstallerInterface;

class XModuleInstaller
{

    private static $inst;


    /**
     * @return ModuleInstallerInterface
     */
    public static function inst()
    {
        if (null === self::$inst) {
            self::$inst = new ModuleInstaller();
        }
        return self::$inst;
    }


}