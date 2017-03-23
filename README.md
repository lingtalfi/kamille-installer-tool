Kamille installer tool
=========================
2017-03-17 --> 2017-03-23




A naive importer/installer for the [kamille framework](https://github.com/lingtalfi/Kamille).

Why naive? Because it doesn't care about version numbers, it always take the latest version.



It imports/installs/uninstalls the modules of kamille in your application.






What can it do for you?
=================

- import a module (and its dependencies)
- install a module (and its dependencies)
- uninstall a module  
- list available modules with description
- search available modules
- list imported modules for a given application
- list installed modules for a given application
- clean the imported modules directories (remove the .git, .gitignore, .DS_Store and .idea files)




How does it work?
=====================

A module must be imported before it can be installed.
Kamille installer can do both operations for you: import/install, or both (and uninstall too).


In which directory are the modules imported? By default, in your application's class-modules directory.

To change the "class-modules" repository to something else, you can use the setrelativemodulepath
command.



Where to find modules?
=========================

To find modules, the fastest way is probably to use the search command.

```bash
kamille search mysearch
```

If you want to search in the description of the module too, use the searchd command instead.
Descriptions often contain tags, like the author name, or the framework used if any.
For instance, to list all modules using the boostrap framework, type this:

```bash
kamille searchd boostrap
```

Or you can go and browse some online repositories directly.
Well, actually there is only one repository:

- https://github.com/KamilleModules/








Setup
==========

In order to use the kamille program, you need to go through the following setup.

In this repository, you will find the "kamille" script.
This is a php script, it contains everything it needs.

- You first need to download it (the kamille script only, you don't need all the repo)
- Then, you need to give it execution permissions (chmod u+x kamille)
- The last step is to put it in your $PATH, so that it can be executed from anywhere (or use symbolic links if you prefer to do so)




Usage
=============

Once the setup is done, you can use the kamille command.

First cd to your app directory.

Then call the kamille command.

Below is the synopsis of the kamille command.



```txt
Usage
-------
kamille import {module}                     # import a module and its dependencies, skip already existing module(s)/dependencies
kamille import -f {module}                  # import a module and its dependencies, replace already existing module(s)/dependencies
kamille install {module}                    # install a module and its dependencies, will import if necessary, skip already existing module(s)/dependencies
kamille install -f {module}                 # install a module and its dependencies, will import if necessary, replace already existing module(s)/dependencies 
kamille uninstall {module}                  # call the uninstall method of the given module 
kamille list {importerAlias}?               # list available modules
kamille listd {importerAlias}?              # list available modules with their description if any
kamille listimported                        # list imported modules
kamille listinstalled                       # list installed modules
kamille search {importerAlias}?             # search through available modules names
kamille searchd {importerAlias}?            # search through available modules names and/or description
kamille clean                               # removes the .git, .gitignore, .idea and .DS_Store files at the top level of your modules' directories
kamille cleanr                              # removes the .git, .gitignore, .idea and .DS_Store files in your modules directories, recursively 

For instance: 
    kamille import Connexion
    kamille import km.Connexion 
    kamille import -f Connexion 
    kamille import -f km.Connexion 
    kamille install Connexion 
    kamille install km.Connexion  
    kamille install -f Connexion 
    kamille install -f km.Connexion 
    kamille uninstall Connexion 
    kamille uninstall km.Connexion
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
    kamille clean
    kamille cleanr
    
    
Options
-------------
-f: when used with the import keyword, force overwriting of existing modules and dependencies. If not set, the Importer will skip existing planets/dependencies.
    when used with the install keyword, force the importing (in force mode too) of the modules
    

```

Note:
as for now, there is only one importer (a reporter basically handle one or more online repositories), and its alias is km (for kamille modules).
The km importer knows how to import/install modules from the KamilleModules online repository.


So now, you can basically import any modules :)






