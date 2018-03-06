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
            'Exception' => [
                'deps' => [],
                'description' => "Widget for displaying an exception",
            ],
            'HtmlCode' => [
                'deps' => [],
                'description' => "Widget to display a free html code",
            ],
            'HttpError' => [
                'deps' => [],
                'description' => "Widget to display an http error",
            ],
            'LoginForm' => [
                'deps' => [],
                'description' => "Widget for displaying a LoginForm",
            ],
            'Maintenance' => [
                'deps' => [],
                'description' => "Widget to display a maintenance text, optional title and optional image",
            ],
            'Notification' => [
                'deps' => [],
                'description' => "Widget for displaying a Notification",
            ],
        ];
    }
}