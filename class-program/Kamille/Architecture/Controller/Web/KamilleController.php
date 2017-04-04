<?php


namespace Kamille\Architecture\Controller\Web;


use Kamille\Architecture\Controller\ControllerInterface;
use Kamille\Architecture\Response\Web\HttpResponse;
use Kamille\Architecture\Response\Web\HttpResponseInterface;
use Kamille\Utils\Laws\LawsUtil;


/**
 * This controller implements standard techniques promoted by the kamille framework
 */
class KamilleController implements ControllerInterface
{


    /**
     * Renders a laws view.
     * More info on laws here: https://github.com/lingtalfi/laws
     *
     *
     * $config: allows you to override the laws config file on the fly.
     *
     * @return HttpResponseInterface
     */
    protected function renderByViewId($viewId, array $config = [])
    {
        return HttpResponse::create(LawsUtil::renderLawsViewById($viewId, $config));
    }

}