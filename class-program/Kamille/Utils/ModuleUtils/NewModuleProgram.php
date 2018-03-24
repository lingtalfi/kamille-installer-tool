<?php


namespace Kamille\Utils\ModuleUtils;


use Bat\FileSystemTool;
use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
use Kamille\Utils\ModuleUtils\Exception\ModuleUtilsException;

class NewModuleProgram
{

    protected $moduleName;


    public static function create()
    {
        return new static();
    }

    public function setModuleName($moduleName)
    {
        $this->moduleName = $moduleName;
        return $this;
    }

    /**
     * @throws ModuleUtilsException
     * @throws \Kamille\Architecture\ApplicationParameters\Exception\ApplicationParametersException
     */
    public function execute()
    {
        $appDir = ApplicationParameters::get("app_dir");
        if ($this->moduleName) {
            $moduleName = $this->moduleName;

            $moduleDir = $appDir . "/class-modules/$moduleName";
            FileSystemTool::mkdir($moduleDir, 0777, true);


            $modulePath = $moduleDir . "/$moduleName" . "Module.php";

            //--------------------------------------------
            // create the module file (only if not exist)
            //--------------------------------------------
            if (!file_exists($modulePath)) {
                $content = $this->getTemplateContent("module", [
                    'moduleName' => $moduleName,
                ]);
                FileSystemTool::mkfile($modulePath, $content);
            }


            //--------------------------------------------
            // import the default pack
            //--------------------------------------------
            $dest = $moduleDir . "/_pack.txt";
            if (!file_exists($dest)) {
                $source = __DIR__ . "/assets/_pack.txt";
                copy($source, $dest);
            }

            //--------------------------------------------
            // import the default conf
            //--------------------------------------------
            $dest = $moduleDir . "/conf.php";
            if (!file_exists($dest)) {
                $source = __DIR__ . "/assets/conf.php";
                copy($source, $dest);
            }

            //--------------------------------------------
            // import the Hooks
            //--------------------------------------------
            $dest = $moduleDir . "/$moduleName" . "Hooks.php";
            if (!file_exists($dest)) {
                $content = $this->getTemplateContent("hooks", [
                    'moduleName' => $moduleName,
                ]);
                FileSystemTool::mkfile($dest, $content);
            }

            //--------------------------------------------
            // import the Services
            //--------------------------------------------
            $dest = $moduleDir . "/$moduleName" . "Services.php";
            if (!file_exists($dest)) {
                $content = $this->getTemplateContent("services", [
                    'moduleName' => $moduleName,
                ]);
                FileSystemTool::mkfile($dest, $content);
            }

            //--------------------------------------------
            // import the README.md
            //--------------------------------------------
            $dest = $moduleDir . "/README.md";
            if (!file_exists($dest)) {
                $content = $this->getTemplateContent("readme", [
                    'moduleName' => $moduleName,
                    'theDate' => date('Y-m-d'),
                ]);
                FileSystemTool::mkfile($dest, $content);
            }


        } else {
            $this->error("Module name not set");
        }
    }



    //--------------------------------------------
    //
    //--------------------------------------------
    protected function getTemplateContent($type, array $params = [])
    {
        switch ($type) {
            case "module":
                $moduleName = $params['moduleName'];
                $tpl = __DIR__ . "/assets/DefaultModule.php";
                $content = file_get_contents($tpl);
                return str_replace('PeiPei', $moduleName, $content);
                break;
            case "hooks":
                $moduleName = $params['moduleName'];
                $tpl = __DIR__ . "/assets/DefaultHooks.php";
                $content = file_get_contents($tpl);
                return str_replace('PeiPei', $moduleName, $content);
                break;
            case "services":
                $moduleName = $params['moduleName'];
                $tpl = __DIR__ . "/assets/DefaultServices.php";
                $content = file_get_contents($tpl);
                return str_replace('PeiPei', $moduleName, $content);
                break;
            case "readme":
                $moduleName = $params['moduleName'];
                $theDate = $params['theDate'];
                $tpl = __DIR__ . "/assets/README.md";
                $content = file_get_contents($tpl);
                return str_replace([
                    'PeiPei',
                    'theDate',
                ], [
                    $moduleName,
                    $theDate,
                ], $content);
                break;
            default:
                $this->error("Unknown template with type=$type");
                break;
        }
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    private function error($msg)
    {
        throw new ModuleUtilsException($msg);
    }
}