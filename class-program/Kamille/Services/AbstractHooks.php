<?php


namespace Kamille\Services;


use Kamille\Services\Exception\XException;
use Kamille\Utils\ModuleInstallationRegister\ModuleInstallationRegister;

class AbstractHooks
{

    /**
     * This method is originally created to access a module's hook.
     * It is meant to be used like this:
     *
     *          a(Hooks::call("Connexion.someHook"));
     *
     * And then, create an Hooks container which extends this AbstractX class,
     * and has a protected static Connexion_someHook method.
     *
     * If param is an array, it will be passed by reference.
     *      You still need to prefix your variable name with the ampersand symbol to benefit the reference mechanism.
     *      If you don't prefix the variable name with the ampersand, it will have the same result as if it was passed by copy.
     * It is assumed that it's an object otherwise, or a scalar value.
     *
     *
     */
    public static function call($hook, &$param = null, $default = null, $throwEx = true)
    {
        $p = explode('_', $hook, 2);
        $error = null;
        if (2 === count($p)) {

            $module = $p[0];
            if (ModuleInstallationRegister::isInstalled($module)) {
                $method = $hook;
                if (method_exists(get_called_class(), $method)) {

                    /**
                     * Note:
                     * the static::class technique does not work in 5.4:  syntax error, unexpected 'class'  (tested in mamp 5.4.45)
                     * It worked on 5.5.38 (mamp).
                     */
                    if (is_array($param)) {
                        return call_user_func_array([static::class, $method], [&$param]);
                    }
                    return call_user_func([static::class, $method], $param);
                } else {
                    $error = "hook not found: $hook";
                }
            } else {
                $error = "Module $module is not installed";
            }
        } else {
            $error = "invalid hook format";
        }
        if (true === $throwEx) {
            throw new XException($error);
        }
        return $default;
    }
}