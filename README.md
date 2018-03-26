Kamille installer tool
=========================
2017-03-17 --> 2018-03-06




A naive importer/installer for the [kamille framework](https://github.com/lingtalfi/Kamille).

Why naive? Because it doesn't care about version numbers, it always take the latest version.



It imports/installs/uninstalls/... the modules and widgets of kamille in your application.



Dependency
===============
This tool will need git install on your machine.



What can it do for you?
=================

- create a kamille application
- import a module (and its dependencies)
- install a module (and its dependencies)
- uninstall a module  
- list available modules with/without description
- search available modules
- list imported modules for a given application
- list installed modules for a given application
- clean the imported modules directories (remove the .git, .gitignore, .DS_Store and .idea files)
- convert the modules to symlinks (if you have a local repository only)
- convert the modules to directories (if you have a local repository only)




How does it work?
=====================
The kamille program is built out of the [ApplicationItemManager](https://github.com/lingtalfi/ApplicationItemManager) planet.
You will find most of the documentation there.






Setup
==========
Before you can use the kamille command, you need to install it on your computer.

First, download the whole repo, and move it where you want.

From there you could already use the kamille script.

The kamille script is just a php script inside the repo, and you could call it directly like this:

```bash
php -f /myphp/kamille-installer-tool/kamille -- help
```

Or we can even call it directly, like this:


```bash
/myphp/kamille-installer-tool/kamille -- help
```

You might need the execute permissions on the kamille script (chmod u+x uni) though.


But in both cases that's too long to type, so instead you should put it in your $PATH.
 
We can use a simple symlink for instance, like this:

```bash
ln -s /myphp/kamille-installer-tool/kamille /usr/local/bin/kamille
```

Now, you can use the kamille command anywhere on your machine:
 
```bash
kamille import AdminTable
``` 

Enjoy ;)



Usage
=============

Once the setup is done, you can use the kamille command.

First cd to your app directory.

Then call the kamille command.



Below is the synopsis of the kamille command.



```txt
Usage
-------

This command can install modules and widgets.



The word item is defined like this:
- item: itemId | itemName
- itemId: repositoryId.itemName | repositoryAlias.itemName




# general
kamille help                                # displays this help
kamille newapp {appName}                    # creates a kamille application with the given name in the current directory
kamille newapp --in-place                   # creates a kamille application inside the current directory (which must be empty)
kamille newmodule --name={moduleName}       # creates a kamille module inside the current application

    The following options are available:
    - morphicPrefix: default:null, defines the morphic prefix to use in conjunction with the --with-morphic option
    - morphicDatabase: default:null, defines the database to use in conjunction with the --with-morphic option.
                    If none is set, morphic will take the default database.


    Then we have optional features that you add with the --with-{featureName} parameters.
    The available options are the following:

    - --with-ecp: creates an ecp eco-system for your module
    - --with-morphic: prepares the morphic translation files for LingFrenchMorphic2Generator.
                        You also need to define the prefix using the --morphicPrefix option.
                        Optionally, you can also define the database with --morphicDatabase



kamille newpage --module={moduleName} ...   # creates a kamille page in the current application

    The "newpage" commands accepts all the paramers below, which you can specify in one of two ways:
    - using the command line args
    - creating a kit-newpage.ini file at the root of your app, those are the default values if the argument is not specified in the command line

    The possible parameters are:

    - module: mandatory, the module name
    - route: default=Dummy_Route     (will be automatically prefixed with the module name)
    - controllerModelDir: default=null, the directory where controller models are looked for
    - controllerModel: default=Dummy, the name of the controller model to use (inside the controllerModelDir)
    - uri: default=null, the uri of the page (if null, will be based on the route
    - controllerDir: default=Pages, the name of the dir in which the controller file will be generated
    - controllerString: default=null, the name of the controller; if null, some default name will be generated based on the route
    - env: default=front (back|front), defines where to create the routes







MODULES
==============
# import/install/update
kamille import {item}                       # import an item and its dependencies, skip already existing item(s)/dependencies
kamille import -f {item}                    # import an item and its dependencies, replace already existing item(s)/dependencies
kamille importall {repoId}?                 # import all items at once, skip already existing item(s)/dependencies
kamille importall {repoId}? -f              # import all items at once, replace already existing item(s)/dependencies
kamille reimport-existing {repoId}?         # re-import all existing items at once, replace already existing item(s)/dependencies
kamille install {item}                      # install an item and its dependencies, will import them if necessary, skip already existing item(s)/dependencies
kamille install -f {item}                   # install an item and its dependencies, will import them if necessary, replace already existing item(s)/dependencies
kamille installall {repoId}?                # install all items at once, will import them if necessary, skip already existing item(s)/dependencies
kamille installall {repoId}? -f             # install all items at once, will import them if necessary, replace already existing item(s)/dependencies
kamille uninstall {item}                    # call the uninstall method on the given item and dependencies
kamille updateall {repoId}?                 # update all items at once, only available for github importer
kamille pack {moduleName}                   # Packs the module {moduleName} from the current application


# list/search
kamille list {repoAlias}?                   # list available items
kamille listd {repoAlias}?                  # list available items with their description if any
kamille listimported                        # list imported items
kamille listinstalled                       # list installed items
kamille search {term} {repoAlias}?          # search through available items names
kamille searchd {term} {repoAlias}?         # search through available items names and/or description

# local (shared) repo
kamille setlocalrepo {repoPath}             # set the local repository path
kamille getlocalrepo                        # print the local repository path
kamille todir                               # converts the top level items of the import directory to directories (based on the directories in local repo)
kamille tolink                              # converts the top level items of the import directory to symlinks to the directories in local repo


# utilities
kamille clean                               # removes the .git, .gitignore, .idea and .DS_Store files in your items directories, recursively
kamille flash                               # equalizes the modules from the local repository to the import directory (so that the import directory contains the same modules as the local repository)



WIDGETS
==============
For widgets, you can use exactly the same commands as for modules, but prefix the command with a w.
For instead, to list all available widgets, do the following:

kamille wlist {repoAlias}?


FLAGS
=========
- f: force
- l: used by the flash method to create symlinks instead of copying the directories
- d: debug mode
- t: trace mode
- v: verbose mode




For instance:
    kamille help

    kamille newapp my-app
    kamille newapp --in-place
    kamille newmodule --name=Alpha
    kamille newpage --module=Alpha --route=My_Route

    kamille import Connexion
    kamille import km.Connexion
    kamille import -f Connexion
    kamille import -f km.Connexion
    kamille importall
    kamille importall -f
    kamille reimport-existing
    kamille install Connexion
    kamille install km.Connexion
    kamille install -f Connexion
    kamille install -f km.Connexion
    kamille installall
    kamille installall -f
    kamille uninstall Connexion
    kamille uninstall km.Connexion
    kamille updateall
    kamille pack Connexion


    kamille list
    kamille list km
    kamille listd
    kamille listd km
    kamille listimported
    kamille listinstalled
    kamille search ling
    kamille search ling km
    kamille searchd kaminos
    kamille searchd kaminos km
    kamille setlocalrepo /path/to/local/repo
    kamille getlocalrepo
    kamille tolink
    kamille todir
    kamille clean
    kamille flash

    kamille wimport Connexion
    kamille wimport km.Connexion
    kamille wimport -f Connexion
    kamille wimport -f km.Connexion
    kamille wimportall
    kamille wimportall -f
    kamille winstall Connexion
    kamille winstall km.Connexion
    kamille winstall -f Connexion
    kamille winstall -f km.Connexion
    kamille winstallall
    kamille winstallall -f
    kamille wuninstall Connexion
    kamille wuninstall km.Connexion
    kamille wupdateall
    kamille wlist
    kamille wlist km
    kamille wlistd
    kamille wlistd km
    kamille wlistimported
    kamille wlistinstalled
    kamille wsearch ling
    kamille wsearch ling km
    kamille wsearchd kaminos
    kamille wsearchd kaminos km
    kamille wsetlocalrepo /path/to/local/repo
    kamille wgetlocalrepo
    kamille wtolink
    kamille wtodir
    kamille wclean

```


So now, you can basically import any modules and widgets :)




Where to find modules and widgets?
===============


There is currently one module repository:

- https://github.com/KamilleModules/

There is currently one widget repository:

- https://github.com/KamilleWidgets/



More doc:
==============
- [newpage](http://www.ling-docs.ovh/kamille/#/tools/morphic) 




