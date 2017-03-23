<?php


namespace ModuleInstaller;


interface ModuleInstallerInterface
{
    public function install($moduleName);

    public function uninstall($moduleName);

    public function isInstalled($moduleName);
}