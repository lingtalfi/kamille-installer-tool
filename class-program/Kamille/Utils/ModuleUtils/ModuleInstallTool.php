<?php


namespace Kamille\Utils\ModuleUtils;


use ApplicationItemManager\ApplicationItemManagerInterface;
use Bat\FileSystemTool;
use CopyDir\SimpleCopyDirUtil;
use DirScanner\DirScanner;
use DirScanner\YorgDirScannerTool;
use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
use Kamille\Module\ModuleInterface;
use Kamille\Utils\ModuleInstallationRegister\ModuleInstallationRegister;
use MethodInjector\MethodInjector;

class ModuleInstallTool
{


    /**
     * Note: maybe we will change the fact that the first argument is an ApplicationItemManagerInterface object,
     * so don't rely too much on it.
     */
    public static function installWidgets(ApplicationItemManagerInterface $manager, array $widgets)
    {
        foreach ($widgets as $widget) {
            $manager->install($widget);
        }
    }


    public static function installConfig(ModuleInterface $module, $replaceMode = true)
    {
        $moduleName = self::getModuleName($module);


        $appDir = ApplicationParameters::get('app_dir');
        if (is_dir($appDir)) {
            $configFile = $appDir . "/class-modules/$moduleName/conf.php";
            if (file_exists($configFile)) {
                $target = $appDir . "/config/modules/$moduleName.conf.php";
                if (true === $replaceMode || false === file_exists($target)) {
                    copy($configFile, $target);
                }
            }
        }
    }

    public static function uninstallConfig(ModuleInterface $module, $replaceMode = true)
    {
        $moduleName = self::getModuleName($module);
        $appDir = ApplicationParameters::get('app_dir');
        $target = $appDir . "/config/modules/$moduleName.conf.php";
        if (is_file($target)) {
            unlink($target);
        }
    }


    /**
     * The idea is to help a module copy its files to the target application.
     * The module must have a directory named "files" at its root, which contains
     * an app directory (i.e. files/app at the root of the module directory).
     *
     *
     * Usage:
     * ---------
     * From your module install code:
     * ModuleInstallTool::installFiles($this);
     *
     *
     * Note: this code assumes that a files step is created.
     *
     */
    public static function installFiles(ModuleInterface $module, $replaceMode = true)
    {

        $moduleName = self::getModuleName($module);

        $appDir = ApplicationParameters::get('app_dir');
        if (is_dir($appDir)) {
            $sourceAppDir = $appDir . "/class-modules/$moduleName/files/app";
            if (file_exists($sourceAppDir)) {
                $o = SimpleCopyDirUtil::create();
                $o->setReplaceMode($replaceMode);
                $ret = $o->copyDir($sourceAppDir, $appDir);
                $errors = $o->getErrors();
            }
        }

    }


    public static function uninstallFiles(ModuleInterface $module, $replaceMode = true)
    {

        $moduleName = self::getModuleName($module);


        $appDir = ApplicationParameters::get('app_dir');
        if (is_dir($appDir)) {
            $sourceAppDir = $appDir . "/class-modules/$moduleName/files/app";
            if (file_exists($sourceAppDir)) {
                DirScanner::create()->scanDir($sourceAppDir, function ($path, $rPath, $level) use ($appDir) {
                    $targetEntry = $appDir . "/" . $rPath;
                    /**
                     * For now we don't follow symlinks.
                     * We also don't delete directories, because we could potentially
                     * remove important app directories.
                     * Maybe this technique will be fine-tuned as time goes by.
                     *
                     */
                    if (
                        file_exists($targetEntry) &&
                        !is_link($targetEntry) &&
                        !is_dir($targetEntry)
                    ) {
                        FileSystemTool::remove($targetEntry);
                    }
                });
            }
        }

    }


    public static function bindModuleServices($moduleServicesClassName)
    {
        $o = new MethodInjector();
        $filter = [
            [\ReflectionMethod::IS_STATIC],
            [\ReflectionMethod::IS_PUBLIC],
        ];


        $methods = $o->getMethodsList($moduleServicesClassName, $filter);
        foreach ($methods as $method) {
            $m = $o->getMethodByName($moduleServicesClassName, $method);
            if (false === $o->hasMethod($m, 'Core\Services\X', $filter)) {
                $c = trim($m->getContent());
                if (0 === stripos($c, 'protected')) {
                    $c = 'public' . substr($c, 9);
                    $m->setContent($c);
                }
                $o->appendMethod($m, 'Core\Services\X');
            }
        }
    }


    public static function unbindModuleServices($candidateModule)
    {
        $o = new MethodInjector();
        $filter = [
            [\ReflectionMethod::IS_STATIC],
            [\ReflectionMethod::IS_PUBLIC],
        ];

        $methods = $o->getMethodsList($candidateModule, $filter);
        foreach ($methods as $method) {
            $m = $o->getMethodByName($candidateModule, $method);
            if (false !== $o->hasMethod($m, 'Core\Services\X', $filter)) {
                $o->removeMethod($m, 'Core\Services\X');
            }
        }
    }


    public static function bindModuleHooks($candidateModule)
    {
        $o = new MethodInjector();
        $filter = [
            [\ReflectionMethod::IS_STATIC],
            [\ReflectionMethod::IS_PROTECTED],
        ];
        /**
         * The strategy here is that hook method which name starts with the module name is a provider method,
         * and other methods are subscriber methods.
         * So for instance for the Core module, one could find the following methods in the CoreHooks class:
         *
         * - Core_hook1
         * - Core_hook2
         * - OtherModule_doSomething
         * - OtherModule2_doSomethingElse
         *
         * The first two methods are provider methods,
         * and the last two methods are subscriber methods to the OtherModule and OtherModule2 modules respectively.
         *
         *
         *
         */

        // list candidate module's methods
        $p = explode('\\', $candidateModule); // Module is the first component
        $module = $p[1];
        $methods = $o->getMethodsList($candidateModule, $filter);
        $providerMethods = [];
        $subscriberMethods = [];
        foreach ($methods as $method) {
            $p = explode('_', $method, 2);
            $moduleName = $p[0];
            if ($module === $moduleName) {
                $providerMethods[] = $method;
            } else {
                $subscriberMethods[$moduleName][] = $method;
            }
        }

        // list application hooks methods
        $appHooksClass = 'Core\Services\Hooks';
        $appHooksMethods = $o->getMethodsList($appHooksClass, $filter);


        // installed modules
        $installed = ModuleInstallationRegister::getInstalled();


        //--------------------------------------------
        // FIRST, BIND PROVIDERS OF THE CANDIDATE MODULE
        //--------------------------------------------
        $filter = [
            [\ReflectionMethod::IS_STATIC],
            [\ReflectionMethod::IS_PUBLIC],
        ];
        foreach ($providerMethods as $method) {
            $m = $o->getMethodByName($candidateModule, $method);
            if (false === $o->hasMethod($m, $appHooksClass, $filter)) {
                $content = trim($m->getContent());
                if (0 === stripos($content, 'protected')) {
                    $content = 'public' . substr($content, 9);
                }


                // compile the method content
                $innerContents = [];
                $innerContents[] = $m->getInnerContent();

                // do other modules want to subscribe to it?
                foreach ($installed as $mod) {
                    $candidateModuleHookClass = 'Module\\' . $mod . '\\' . $mod . 'Hooks';
                    if (false !== ($mSource = $o->getMethodByName($candidateModuleHookClass, $method))) {
                        $innerContent = $mSource->getInnerContent();
                        // prepare inner content
                        $startComment = self::getHookComment($mod, "start");
                        $endComment = self::getHookComment($mod, "end");
                        $innerContent = $startComment . $innerContent . PHP_EOL . trim($endComment);
                        $innerContents[] = $innerContent;
                    }
                }


                $p = explode('{', $content, 2);
                $start = trim($p[0]) . PHP_EOL . "\t{";
                $end = "\t}";
                $innerContents = array_filter($innerContents);
                $body = implode(PHP_EOL, $innerContents);
                $body = self::trimMethodContent($body);
                $lines = explode(PHP_EOL, $body);
                $body = "\t\t" . implode(PHP_EOL . "\t\t", $lines);
                $content = $start . PHP_EOL . $body . PHP_EOL . $end;

                $m->setContent($content);
                $o->appendMethod($m, $appHooksClass);
            }
        }


        //--------------------------------------------
        // BIND SUBSCRIBERS OF THE CLASS BEING INSTALLED
        //--------------------------------------------
        foreach ($installed as $mod) {
            if (array_key_exists($mod, $subscriberMethods)) {
                $methods = $subscriberMethods[$mod];
                $installedHooksClassName = 'Core\Services\Hooks';
                foreach ($methods as $method) {
                    if (false !== ($m = $o->getMethodByName($installedHooksClassName, $method))) {
                        // take the inner content, and add it to the target module's hook method

                        if (false !== ($mSource = $o->getMethodByName($candidateModule, $method))) {
                            $innerContent = $mSource->getInnerContent();

                            // does the target hook class already contain the hook?
                            $startComment = self::getHookComment($module, "start");
                            $targetInnerContent = $m->getInnerContent();

                            if (false === strpos($targetInnerContent, $startComment)) { // if not, we add the hook

                                $innerContent = self::trimMethodContent($innerContent);
                                $targetInnerContent = self::trimMethodContent($targetInnerContent);
                                $endComment = self::getHookComment($module, "end");
                                $innerContent = $startComment . $innerContent . PHP_EOL . $endComment;
                                $targetInnerContent .= PHP_EOL . $innerContent;
                                $targetInnerContent = trim($targetInnerContent);

                                $o->replaceMethodByInnerContent($installedHooksClassName, $method, $targetInnerContent);
                            }
                        }
                    }
                }
            }
        }
    }

    public static function unbindModuleHooks($candidateModule)
    {
        $targetClass = 'Core\Services\Hooks';
        $p = explode('\\', $candidateModule); // Module is the first component
        $module = $p[1];


        $o = new MethodInjector();
        $filter = [
            [\ReflectionMethod::IS_STATIC],
            [\ReflectionMethod::IS_PUBLIC],
        ];
        $hooksMethods = $o->getMethodsList($targetClass, $filter);

        // unbind code subscribing the candidateModule to other module's hooks
        foreach ($hooksMethods as $method) {
            if (0 !== strpos($method, $module . "_")) {
                if (false !== ($m = $o->getMethodByName($targetClass, $method))) {

                    $innerContent = $m->getInnerContent();
                    // does the target hook class already contain the hook?
                    $startComment = self::getHookComment($module, "start");
                    $startComment = trim($startComment);

                    if (false !== strpos($innerContent, $startComment)) {
                        $endComment = self::getHookComment($module, "end");
                        $endComment = trim($endComment);


                        $pattern = '!' . $startComment . '.*' . $endComment . '!Ums';
                        $innerContent = preg_replace($pattern, '', $innerContent);
                        $innerContent = self::trimMethodContent($innerContent);
                        $innerContent = trim($innerContent);
                        $o->replaceMethodByInnerContent($targetClass, $method, $innerContent);
                    }
                }
            }
        }


        /**
         * Then unbind providers.
         * I don't know why but unbinding providers has to be done AFTER unbinding subscribers (with the current code
         * at least).
         *
         *
         * Also, with the current cut technique, the methods have to be removed from the
         * last one (at the bottom) to the first one (at the top).
         *
         *
         *
         */
        // unbind providers provided by the candidate module
        $methodsToRemove = [];
        foreach ($hooksMethods as $method) {
            if (0 === strpos($method, $module . "_")) {
                if (false !== ($m = $o->getMethodByName($targetClass, $method))) {
                    $methodsToRemove[] = $m;
                }
            }
        }
        $o->removeMethods($methodsToRemove, $targetClass);
    }

    public static function installControllers($moduleName)
    {
        $appDir = ApplicationParameters::get("app_dir");
        $controllersDir = $appDir . "/class-modules/$moduleName/Controller";
        if (is_dir($controllersDir)) {
            $files = YorgDirScannerTool::getFilesWithExtension($controllersDir, "php", false, true, true);
            foreach ($files as $f) {
                $file = $controllersDir . "/$f";
                if ('Controller.php' === substr($file, -14)) {
                    $c = file_get_contents($file);

                    /**
                     * non safe namespace replacing technique, but should work 98% of the time,
                     * good for now...
                     */
                    $newNamespace = "namespace Controller\\$moduleName;";
                    $c = preg_replace('!namespace .*;!', $newNamespace, $c, 1);
                    $targetFile = $appDir . "/class-controllers/$moduleName/$f";
                    FileSystemTool::mkfile($targetFile, $c);
                }
            }
        }
    }


    /**
     * I believe you don't want to remove userland code,
     * otherwise some users might get VERY upset!
     */
//    public static function uninstallControllers($moduleName)
//    {
//        $appDir = ApplicationParameters::get("app_dir");
//        $controllersDir = $appDir . "/class-controller/$moduleName";
//        if (is_dir($controllersDir)) {
//            FileSystemTool::remove($controllersDir);
//        }
//    }


    //--------------------------------------------
    //
    //--------------------------------------------
    private static function trimMethodContent($content)
    {
        $p = explode(PHP_EOL, $content);
        $p = array_map(function ($v) {
            return trim($v);
        }, $p);
        return implode(PHP_EOL, $p);
    }

    private static function getHookComment($module, $type = "start")
    {
        return '// mit-' . $type . ':' . $module . PHP_EOL;
    }


    private static function getModuleName(ModuleInterface $module)
    {
        $moduleClassName = get_class($module);
        $p = explode('\\', $moduleClassName);
        array_shift($p); // drop Module prefix
        return $p[0];
    }
}