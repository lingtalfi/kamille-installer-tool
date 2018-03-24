<?php


namespace Kamille\Utils\Console;


use Bat\CaseTool;
use Bat\FileSystemTool;
use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
use Kamille\Exception\KamilleException;
use Kamille\Utils\Routsy\Util\ConfigGenerator\ConfigGenerator;

class PageCreatorProgram
{

    protected $module;
    protected $routeId;
    protected $url;
    protected $controllerString;
    protected $controllerModel;
    //
    protected $controllerDir;
    protected $controllerModelDir;
    protected $env;
    protected $appDir;


    public function __construct()
    {
        $this->module = "ThisApp";
        $this->routeId = "my_route";
        $this->url = null;
        $this->controllerString = null;
        $this->controllerModel = "Dummy"; // look in assets directory
        $this->controllerDir = "Pages";
        $this->controllerModelDir = null;
        $this->env = "front";
    }

    public static function create()
    {
        return new static();
    }


    public function execute()
    {
        $module = $this->module;
        $env = $this->env;
        if ('front' === $env) {
            $env = "routes";
        }
        $routeId = $this->routeId;
        $url = $this->url;
        $controllerString = $this->controllerString;
        $controllerDir = $this->controllerDir;
        $controllerModel = $this->controllerModel;
        $controllerModelDir = $this->controllerModelDir;
        $appDir = ApplicationParameters::get("app_dir");


        //--------------------------------------------
        // CREATE ROUTE
        //--------------------------------------------
        // resolve automatic guesses
        if (null === $url) {
            $url = "/" . strtolower($controllerDir) . "/" . str_replace('_', '-', strtolower($module . "_" . $routeId));
        }
        if (null === $module) {
            $module = "ThisApp";
        }
        if (null === $controllerModelDir) {
            $controllerModelDir = __DIR__ . '/assets';
        }
        $controllerModelDir = str_replace('[app]', $appDir, $controllerModelDir);

        $controllerPath = "\Controller\\$module";

        if (null === $controllerString) {
            $controllerName = CaseTool::snakeToFlexiblePascal($routeId);
            $controllerString = "$controllerName:render";
        }


        if (0 !== strpos($controllerString, ":")) { // relative path
            $controllerPath .= "\\$controllerDir";
        } else {
            $controllerString = ltrim($controllerString, ':');
        }
        $controllerPath .= "\\$controllerString";


        $routeId = $module . "_" . $routeId;

        // first, insert a route
        $routeContent = '$routes["' . $routeId . '"] = ["' . $url . '", null, null, \'' . $controllerPath . '\'];';
        $routsyFile = $appDir . "/config/routsy/$env.php";
        $section = "Module $module"; //
        ConfigGenerator::addSectionIfNotExist($routsyFile, $section, "MODULES");
        ConfigGenerator::addRouteToRoutsyFile($routeId, $routeContent, $routsyFile, $section);


        //--------------------------------------------
        // CREATE CONTROLLER
        //--------------------------------------------
        $p = explode(':', $controllerPath);
        $controllerNamespaceClass = $p[0];
        $controllerNamespaceClass = ltrim($controllerNamespaceClass, '\\');
        $p = explode('\\', $controllerNamespaceClass);
        $q = $p;
        array_shift($q); // remove Controller prefix
        $path = implode('\\', $q);
        $className = array_pop($p);
        $controllerNamespaceParent = implode('\\', $p);

        $controllerFile = $appDir . "/class-controllers/" . str_replace('\\', '/', $path) . '.php';
        $controllerModelFile = $controllerModelDir . "/$controllerModel" . "ControllerModel.tpl.php";
        if (file_exists($controllerModelFile)) {
            $content = file_get_contents($controllerModelFile);
            $content = str_replace([
                '_controllerNamespace_',
                '_controllerClassname_',
            ], [
                $controllerNamespaceParent,
                $className,
            ], $content);

            FileSystemTool::mkfile($controllerFile, $content);
        } else {
            $this->error("controller model not found: $controllerModelFile");
        }


        return [
            "routeId" => $routeId,
            "url" => $url,
            "controllerFile" => $controllerFile,
            "routsyFile" => $routsyFile,
        ];
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    public function setControllerModelDir($controllerModelDir)
    {
        $this->controllerModelDir = $controllerModelDir;
        return $this;
    }

    public function setControllerModel($controllerModel)
    {
        $this->controllerModel = $controllerModel;
        return $this;
    }


    public function setModule($module)
    {
        $this->module = $module;
        return $this;
    }

    public function setRouteId($routeId)
    {
        $this->routeId = $routeId;
        return $this;
    }

    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    public function setControllerString($controllerString)
    {
        $this->controllerString = $controllerString;
        return $this;
    }

    public function setControllerDir($controllerDir)
    {
        $this->controllerDir = $controllerDir;
        return $this;
    }

    public function setEnv($env)
    {
        $this->env = $env;
        return $this;
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    protected function error($msg)
    {
        throw new KamilleException($msg);
    }
}