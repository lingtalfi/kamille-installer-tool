<?php


namespace Kamille\Architecture\RequestListener\Web;


use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
use Kamille\Architecture\Request\Web\HttpRequestInterface;
use Kamille\Architecture\Router\RouterInterface;
use Kamille\Services\XLog;


/**
 * This is a router for a web application.
 *
 * It sets the controller parameter in the request (if a route matches),
 * or do nothing special otherwise.
 *
 * The controller is a callable.
 *
 * Also, it can attach an urlParam to the request.
 *
 * urlParams are parameters that are found in the url, but are different from $_GET.
 * They often serve the purpose of allowing "pretty" url.
 *
 * For instance:
 * - http://mysite.com/post-about-how-i-killed-my-cat
 *
 * instead of:
 * - http://mysite.com?page=6
 *
 *
 * Note that there could be many router request listeners,
 * so we MERGE the urlParams instead of REPLACING them.
 *
 *
 *
 *
 */
class RouterRequestListener implements HttpRequestListenerInterface
{

    /**
     * @var RouterInterface[]
     */
    private $routers;

    public function __construct()
    {
        $this->routers = [];
    }

    public static function create()
    {
        return new static();
    }

    public function listen(HttpRequestInterface $request)
    {
        $controller = null;
        $urlParams = [];
        foreach ($this->routers as $router) {
            if (null !== ($res = $router->match($request))) {


                if (is_array($res)) {
                    $controller = $res[0];
                    $urlParams = $res[1];
                    break;

                } elseif (is_string($res)) {
                    $controller = $res;
                    break;
                }
            }
        }

        if (null !== $controller) {


            if (true === ApplicationParameters::get('debug')) {
                $s = "unknown controller type";
                if (is_string($controller)) {
                    $s = $controller;
                } elseif (is_array($controller)) {
                    $cont = $controller[0];
                    if (is_object($cont)) {
                        $s = get_class($cont);
                    } elseif (is_string($cont)) {
                        $s = $cont;
                    }
                }
                XLog::debug("RouterRequestListener: Router matched: " . get_class($router) . ", controller: $s");
            }


            $request->set("controller", $controller);
            $urlParams = array_merge($request->get('urlParams', []), $urlParams);
            $request->set("urlParams", $urlParams);
        }
    }

    public function addRouter(RouterInterface $router)
    {
        $this->routers[] = $router;
        return $this;
    }

}