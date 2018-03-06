<?php


namespace KamilleProgram\Repository;


use ApplicationItemManager\Repository\AbstractRepository;

class KamilleModulesRepository extends AbstractRepository
{

    public function getName()
    {
        return 'KamilleModules';
    }


    //--------------------------------------------
    // OVERRIDE THOSE METHODS
    //--------------------------------------------
    protected function createItemList()
    {
        return [
            'Application' => [
                'deps' => [],
                'description' => <<<EEE
This module let you make application level decisions.
Tags: kamille-app; lingtalfi
EEE
                ,
            ],
            'AutoAdmin' => [
                'deps' => [],
                'description' => <<<EEE
A tool to generate a phpMyAdmin like admin.
Tags: kamille-app; lingtalfi; crud; phpMyAdmin
EEE
                ,
            ],
            'Authenticate' => [
                'deps' => [],
                'description' => <<<EEE
This module brings the authenticate system to your kamille application.
Tags: kamille-app; lingtalfi; authenticate
EEE
                ,
            ],
            'Connexion' => [
                'deps' => [
//                    '+KamilleModules.GentelellaWebDirectory',
                ],
                'description' => <<<EEE
This module allows the user to log into the kamille application, via a login form.
It uses the Privilege framework under the hood.
Tags: kamille-app; lingtalfi
EEE
                ,
            ],
            'Core' => [
                'deps' => [],
                'description' => <<<EEE
This module boots your kamille application. It wraps the dispatching loop and provides code level levers. 
Tags: kamille-app; lingtalfi
EEE
                ,
            ],
            'DataTable' => [
                'deps' => [],
                'description' => <<<EEE
A module to bring datatables to your kamille app 
Tags: kamille-app; lingtalfi
EEE
                ,
            ],
            'Ekom' => [
                'deps' => [],
                'description' => <<<EEE
An e-commerce module for the kamille framework  
Tags: kamille-app; lingtalfi; e-commerce
EEE
                ,
            ],
            /**
             * Removed because too heavy (and not used)
             */
//            'GentelellaWebDirectory' => [
//                'deps' => [],
//                'description' => <<<EEE
//This module imports the gentelella admin theme into the web directory of your application.
//Tags: theme; bootstrap
//EEE
//                ,
//            ],
            'NullosAdmin' => [
                'deps' => [
                    "KamilleModules.UploadProfile",
                ],
                'description' => <<<EEE
NullosAdmin is an admin website
Tags: admin
EEE
                ,
            ],
            'UploadProfile' => [
                'deps' => [],
                'description' => <<<EEE
This module implements an uploading strategy for your application.
Tags: upload
EEE
                ,
            ],
        ];
    }
}