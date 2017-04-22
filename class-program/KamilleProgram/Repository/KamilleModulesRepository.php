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
            'Connexion' => [
                'deps' => [
                    '+KamilleModules.GentelellaWebDirectory',
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
            'GentelellaWebDirectory' => [
                'deps' => [],
                'description' => <<<EEE
This module imports the gentelella admin theme into the web directory of your application.
Tags: theme; bootstrap
EEE
                ,
            ],
            'NullosAdmin' => [
                'deps' => [],
                'description' => <<<EEE
NullosAdmin is an admin website
Tags: admin
EEE
                ,
            ],
        ];
    }
}