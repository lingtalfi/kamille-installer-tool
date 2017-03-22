Implementation notes
=======================
2017-03-22




- Importer



Importer
-----------
The importer role is to fetch distant modules, and put them in a given target directory.
An importer is able a module and all its dependencies (i.e. the dependencies can also be imported by the same module).
Importer have a full name, and aliases.

### solving module name conflicts
The aliases help differentiate which importer is used.
When the kamille program is executed, it can potentially use as many importers as it wants.
So imagine importer A handles a module named Connexion, and importer B also has a module named Connexion.

That would be a bad thing in general, because with the current system, you can't use two modules with the same name.
In other words, you'll have to choose whether you use the Connexion module from importer A, or the Connexion module
from importer B, but your application can only use one Connexion module (and its arguably a good thing).

So, an alias for importer A could be a (the letter a), and an alias for importer B could be the letter b (for instance).

And so, with kamille, we can type the following things to import the Connexion module into the application land:
 
- kamille import Connexion          # will fail, because in this example the Connexion module is found in more than one importer: there is ambiguity
- kamille import a.Connexion        # will import module from importer A, there is no more ambiguity
- kamille import b.Connexion        # will import module from importer B


So, that's the power of aliases, and the reason why they exist.




Kamille Naive Importer - Synopsis
-------------------------
With the kamille program, you can basically do two things:

- import
- install

In addition to this, you can use the -f flag, which forces the re-import or re-install and basically ensures a fresh import and/or install.

Let's see the details of what each command does, without and with the -f flag.



command     |  description
------------| ---------
import      | ensures that the module and all its dependencies are imported. Ignore already imported modules.
import -f   | remove the module directory, and the directories of all dependencies, then trigger the import command.
install     | ensures that the module and all dependencies are installed. Will import them if necessary. If a module is already installed, it does not call its install method again.
install -f  | remove the module directory, and the directories of all dependencies, then call the install method for every module (even if they were already installed).
            
            
Note that removing a module directory doesn't properly uninstall it.



ProgramLog
----------------
The program log is the logger service used by every component of the kamille naive importer program.
It can send different types of messages: warn, error, info, debug.
And we can choose its verbosity, based on the message types.

In quiet mode, only errors are displayed, but the default mode shows all message types except debug.
The debug mode is activated from the code only.








