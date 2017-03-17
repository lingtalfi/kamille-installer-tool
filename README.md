Kamille installer tool
=========================
2017-03-17


Work in progress...


A naive importer/installer for the [kamille framework](https://github.com/lingtalfi/Kamille).

Why naive? Because it doesn't care about version numbers, it always take the latest version.




It imports/installs/uninstalls the modules of kamille in your application.






What can it do for you?
=================

- import a module (and its dependencies)
- install a module (and its dependencies)
- uninstall a module  
- list all imported modules for a given application
- list all installed modules for a given application
- clean the imported modules directories (remove the .git, .gitignore, .DS_Store and .idea files)




How does it work?
=====================

A module must be imported before it can be installed.
This installer can do both operations for you: import or install (or uninstall too).


Where are the modules imported? By default, in your application's class-modules directory.

To change the "class-modules" repository relative location to something else, you can use the setrelativemodulepath
command.




Setup
==========

In this repository, you will find the "kamille" script.
This is a php script.

- You need first to download it
- Then, you need to give it execution permissions (chmod u+x kamille)
- The last step is to put it in your $PATH, so that it can be executed from anywhere (or use symbolic links if you prefer to do so)




Usage
=============

Once the setup is done, you can use the kamille command.

First cd to your app directory.

Then call the kamille command.

Below is the synopsis (from the help of the command)

```txt
Usage
-------
kamille import {module} {importerId}?                    # import one module and dependencies, skip already existing module(s)/dependencies
kamille import -f {module} {importerId}?                 # import one module and dependencies, replace already existing module(s)/dependencies
kamille install {module} {importerId}?                   # call the install method of the given module (it fails if the module is not imported already)
kamille install -f {module} {importerId}?                # import and install one module and all its dependencies 
kamille list {importerId}?                               # list available modules
kamille listimported                                     # list imported modules
kamille listinstalled                                    # list installed modules
kamille setmodulesrelpath                                # set the relative path to the modules directory (from the app directory)
kamille getmodulesrelpath                                # get the relative path to the modules directory (from the app directory)
kamille clean                                            # removes the .git, .gitignore, .idea and .DS_Store files at the top level of your application's directory

For instance: 
    kamille import Connexion
    kamille import Connexion KamilleWidgets
    kamille import -f Connexion 
    kamille import -f Connexion KamilleWidgets
    kamille install Connexion 
    kamille install Connexion KamilleWidgets 
    kamille install -f Connexion 
    kamille install -f Connexion KamilleWidgets
    kamille listimported 
    kamille listinstalled                      
    kamille setmodulesrelpath
    kamille getmodulesrelpath
    kamille clean
    
    
Options
-------------
-f: when used with the import keyword, force overwriting of existing modules and dependencies. If not set, the Importer will skip existing planets/dependencies.
    when used with the install keyword, force the importing (in force mode too) of the modules
    

```



By default, this will create the class-modules directory inside your app.

So now, you can basically import any modules :)






