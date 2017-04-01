<?php


namespace KamilleProgram\Repository;


use ApplicationItemManager\Repository\AbstractRepository;

class KamilleWidgetsRepository extends AbstractRepository
{
    public function getName()
    {
        return 'KamilleWidgets';
    }


    //--------------------------------------------
    // OVERRIDE THOSE METHODS
    //--------------------------------------------
    protected function createItemList()
    {
        return [
            'BookedMeteo' => [
                'deps' => [],
                'description' => "Widget to display the weather conditions for your city",
            ],
        ];
    }
}