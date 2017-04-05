<?php


namespace ApplicationItemManager;

use ApplicationItemManager\Exception\ApplicationItemManagerException;


interface KamilleApplicationItemManagerInterface extends ApplicationItemManagerInterface
{

    /**
     * Import an widget and its dependencies in the import directory.
     * If force is false, will not try to replace already imported widgets.
     * If force is true, will replace already imported widgets before importing them.
     *
     */
    public function wimport($widget, $force = false);

    /**
     * Import all widgets at once.
     *
     * if repoId is specified, it constrains the import to the specified repoId(s).
     * repoId can be a string identifying a specific repository name, or an array of repository names.
     *
     *
     * @return bool, true if everything went right, and false otherwise
     */
    public function wimportAll($repoId = null, $force = false);

    /**
     * Install an widget and its dependencies.
     * If force is false, will not try to re-install already installed widgets.
     * If force is true, will (re-)install all widgets, even those already installed.
     *
     */
    public function winstall($widget, $force = false);


    /**
     * Install all widgets at once.
     *
     * if repoId is specified, it constrains the import to the specified repoId(s).
     * repoId can be a string identifying a specific repository name, or an array of repository names.
     *
     *
     * @return bool, true if everything went right, and false otherwise
     */
    public function winstallAll($repoId = null, $force = false);

    /**
     * Uninstall an widget and its hard dependencies.
     * See documentation for more info on hard dependencies.
     */
    public function wuninstall($widget);


    /**
     * @return array, of available widgets for the repository if specified, or for all directories if repo is null.
     *
     * The type of array returned depends on the value of the keys argument:
     * - if keys is null, returns a one dimension array containing the available widgetIds of every repository bound
     *          to this instance
     * - if keys is an array, it represents the keys that will be present in every entry of the returned array.
     *          If you specify a key that isn't provided by a repository, the value null will be returned instead.
     *          The deps key is not available (i.e. you cannot get the dependencies as for now with this method).
     *
     *
     *
     * @throws ApplicationItemManagerException if the repoId is not a valid repository
     */
    public function wlistAvailable($repoId = null, array $keys = null);


    /**
     * @return array, list of imported widgetName
     */
    public function wlistImported();

    /**
     * @return array, list of installed widgetName
     */
    public function wlistInstalled();

    /**
     *
     * Search should be case insensitive, although it can depend on the implementation.
     *
     *
     *
     * - keys: an array of where to search, and also what to return.
     *          If null, will search the widget and return a one dimensional array containing only matching widgets.
     *
     *          If an array, return a multidimensional array (with keys being the widgetIds) containing the specified keys.
     *          You need to specify the "widget" special key in the array if you want to search in the widgetName.
     *
     *          Depending on your list, you might have keys like for instance:
     *              - widget
     *              - description
     *              - other things depending on what the list has
     *
     *          If a key is specified and it doesn't exist in the list, it does not return
     *          an error, but simply ignores the key, and actually returns null.
     *
     *
     * - repoId: if not null, constrain the search only to the specified repoId
     *
     */
    public function wsearch($text, array $keys = null, $repoId = null);
}




















