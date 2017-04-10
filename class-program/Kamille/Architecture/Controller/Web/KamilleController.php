<?php


namespace Kamille\Architecture\Controller\Web;


use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
use Kamille\Architecture\Controller\ControllerInterface;
use Kamille\Architecture\Response\Web\HttpResponse;
use Kamille\Architecture\Response\Web\HttpResponseInterface;
use Kamille\Services\XLog;
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
    protected function renderByViewId($viewId, $config = null, array $options = [])
    {
        if (true === ApplicationParameters::get('debug')) {
            XLog::debug("[Controller " . get_called_class() . "] - renderByViewId with viewId $viewId");
        }
        return HttpResponse::create(LawsUtil::renderLawsViewById($viewId, $config, $options));
    }

}