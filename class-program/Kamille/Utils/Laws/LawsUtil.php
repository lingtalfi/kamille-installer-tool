<?php


namespace Kamille\Utils\Laws;


use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
use Kamille\Ling\Z;
use Kamille\Mvc\HtmlPageHelper\HtmlPageHelper;
use Kamille\Mvc\Layout\HtmlLayout;
use Kamille\Mvc\LayoutProxy\LawsLayoutProxy;
use Kamille\Mvc\Loader\FileLoader;
use Kamille\Mvc\Position\Position;
use Kamille\Mvc\Renderer\PhpLayoutRenderer;
use Kamille\Mvc\Widget\Widget;
use Kamille\Services\XLog;
use Kamille\Utils\Laws\Exception\LawsUtilException;


class LawsUtil
{


    /**
     * $config: callable|array
     *          If it's an array, it will be merged with the laws config array.
     *          If it's a callable, the laws config array will be passed by reference as the argument of that callable.
     *
     *
     */
    public static function renderLawsViewById($viewId, $config = null, array $options = [])
    {

        $appDir = ApplicationParameters::get("app_dir");
        $file = $appDir . "/config/laws/$viewId.conf.php";
        if (file_exists($file)) {
            $conf = [];
            include $file;

            if (is_array($config)) {
                $conf = array_replace_recursive($conf, $config);
            } elseif (is_callable($config)) {
                call_user_func_array($config, [&$conf]);
            }
            return self::renderLawsView($conf, $viewId, $file, $options);
        }
        throw new LawsUtilException("laws config file not found: $file");
    }


    /**
     * $variables: allow us to change config on the fly (i.e. from the controller).
     *      it can contain the following keys:
     *          - layout: yourConf
     *          - widgets
     *              - widgetId: yourConf
     *          - positions:
     *              - positionName: yourConf
     *
     *
     */
    private static function renderLawsView(array $config, $viewId = null, $file = null, array $options = [])
    {
        $options = array_merge([
            'autoloadCss' => true,
            'widgetClass' => 'Kamille\Mvc\Widget\Widget',
        ], $options);
        $autoloadCss = $options['autoloadCss'];
        $widgetClass = $options['widgetClass'];


        $layoutTemplate = $config['layout']['name'];
        $positions = (array_key_exists('positions', $config)) ? $config['positions'] : [];
        $widgets = (array_key_exists('widgets', $config)) ? $config['widgets'] : [];
        $layoutConf = (array_key_exists('conf', $config['layout'])) ? $config['layout']['conf'] : [];

        $theme = ApplicationParameters::get("theme");
        $wloader = FileLoader::create()->addDir(Z::appDir() . "/theme/$theme/widgets");
        $ploader = FileLoader::create()->addDir(Z::appDir() . "/theme/$theme/positions");


        $commonRenderer = PhpLayoutRenderer::create();
        $proxy = LawsLayoutProxy::create()->setRenderer($commonRenderer);


        if (true === ApplicationParameters::get('debug')) {

            $sWidgets = "";
            foreach ($widgets as $id => $widgetInfo) {
                $name = "unknown";
                if (true === array_key_exists('name', $widgetInfo)) {
                    $name = $widgetInfo["name"];
                }
                $sWidgets .= PHP_EOL . "----- id: $id; tplName: $name";
            }

            $viewIdFile = $file;
            if (null !== $viewIdFile) {
                $appDir = ApplicationParameters::get("app_dir");
                $viewIdFile = str_replace($appDir, '', $viewIdFile);
                $viewIdFile = ' (' . $viewIdFile . ')';
            }

            $sPos = "";
            $c = 0;
            foreach ($positions as $name => $info) {
                if (0 !== $c) {
                    $sPos .= ", ";
                }
                $sPos .= "name: $name; tplName: " . $info['name'];
                $c++;
            }


            $trace = [];
            $theme = ApplicationParameters::get("theme", "no theme");
            $trace[] = "LawsUtil trace with theme: $theme, viewId: $viewId" . $viewIdFile . ":";
            $trace[] = "- layout: $layoutTemplate";
            $trace[] = "- positions: " . $sPos;
            $trace[] = "- widgets: " . $sWidgets;


            XLog::trace(implode(PHP_EOL, $trace));
        }


        //--------------------------------------------
        // LAYOUT
        //--------------------------------------------
        $layout = HtmlLayout::create()
            ->setTemplate($layoutTemplate)
            ->setLoader(FileLoader::create()
                ->addDir(Z::appDir() . "/theme/$theme/layouts")
            )
            ->setRenderer($commonRenderer);

        if (true === $autoloadCss) {
            $p = explode("/", $layoutTemplate);
            $css = "theme/$theme/layouts/" . $p[0] . "/" . $p[0] . '.' . $p[1] . ".css";
            if (file_exists(Z::appDir() . "/www/$css")) {
                HtmlPageHelper::css("/$css");
            }
        }

        //--------------------------------------------
        // POSITIONS
        //--------------------------------------------
        foreach ($positions as $positionName => $pInfo) {
            $tplName = $pInfo['name'];
            $pVars = (array_key_exists('conf', $pInfo)) ? $pInfo['conf'] : [];

            $proxy->bindPosition($positionName, Position::create()
                ->setTemplate($tplName)
                ->setLoader($ploader)
                ->setVariables($pVars)
                ->setRenderer($commonRenderer));


            if (true === $autoloadCss) {
                $p = explode("/", $tplName);
                $css = "theme/$theme/positions/" . $p[0] . "/" . $p[0] . '.' . $p[1] . ".css";
                if (file_exists(Z::appDir() . "/www/$css")) {
                    HtmlPageHelper::css("/$css");
                }
            }
        }
        $commonRenderer->setLayoutProxy($proxy);


        //--------------------------------------------
        // WIDGETS
        //--------------------------------------------
        foreach ($widgets as $id => $widgetInfo) {
            if (true === array_key_exists('name', $widgetInfo)) {

                $name = $widgetInfo['name'];
                $conf = (array_key_exists('conf', $widgetInfo)) ? $widgetInfo['conf'] : [];


                $wid = new $widgetClass;

                $layout
                    ->bindWidget($id, $wid
                        ->setTemplate($name)
                        ->setVariables($conf)
                        ->setLoader($wloader)
                        ->setRenderer($commonRenderer)
                    );


                if (true === $autoloadCss) {
                    $p = explode("/", $name);
                    $css = "theme/$theme/widgets/" . $p[0] . "/" . $p[0] . '.' . $p[1] . ".css";
                    if (file_exists(Z::appDir() . "/www/$css")) {
                        HtmlPageHelper::css("/$css");
                    }
                }


            } else {
                $end = (null !== $viewId) ? " (viewId=$viewId)" : "";
                XLog::error("LawsUtil: name is not a valid key for widgetId $id" . $end);
            }
        }

        return $layout->render($layoutConf);

    }

}