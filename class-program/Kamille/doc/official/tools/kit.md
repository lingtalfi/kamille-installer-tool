Kit
===========
2018-03-14


Kamille Installer Tool (kit), est un outil en ligne de commande permettant d'exécuter certaines tâches ennuyantes.


Parmi les tâches que kit peut réaliser, nous allons nous intéresser aux suivantes:

- créer une page 
- créer une application de base 
- importer un module 
- empaqueter un module 



Installation
--------------

Voir la documentation sur le [repository de kit](https://github.com/lingtalfi/kamille-installer-tool).



Créer une page
----------------

La méthode `newpage` permet de créer une nouvelle page.

Cette méthode va effectuer les tâches suivantes:

- insérer une route dans le fichier de routes routsy de l'application:
    - soit `config/routsy/back.php` si on choisit le backoffice
    - soit `config/routsy/routes.php` si on choisit le frontoffice
- créer le contrôleur correspondant à cette route, par défaut dans le dossier `class-controllers/ThisApp/Pages`




Voici la commande abstraite:

```bash
kamille newpage {routeId} {url}? {controllerString}? {module}? -e={env}?
```

- `routeId`: l'id de la route à créer. Celle-ci sera automatiquement préfixée par le nom du module
- `url`: l'url par laquelle accéder à cette page. Si non renseignée, elle sera devinée d'après routeId (les underscores seront remplacés par des tirets)
- `controllerString`: &lt;controllerName> &lt;:> &lt;method>
- `controllerName`: (&lt;absolutePrefix>)? &lt;controllerName>
    - si absolutePrefix est défini, le chemin du contrôleur  sera absolu (ayant pour namespace de base `Controller\$ModuleName`)
    - si absolutePrefix n'est pas défini, le chemin du contrôleur sera relatif par rapport au namespace `Controller\$ModuleName\$controllerDir`
    - $controllerDir est défini par le développeur dans le fichier de configuration, et vaut par défaut "Pages".
- `absolutePrefix`: ":" (le caractère deux-points)
- `module`: le module à utiliser. Par défaut: `ThisApp`
- `env`: l'environnement à utiliser, qui représente le fichier routsy à utiliser (valeurs possibles: back, routes)


#### Examples

```bash
 
kamille newapp my_page 
kamille newapp my_page /path/to/my-page
kamille newapp my_page /path/to/my-page Koo:render      # création du contrôleur dans Controller\ThisApp\Pages
kamille newapp my_page /path/to/my-page :Koo:render     # création du contrôleur directement dans Controller\ThisApp
kamille newapp my_page /path/to/my-page Koo:render -e=back 

```

#### Le fichier de configuration

Cette commande peut travailler avec un fichier de configuration, qui permet d'éviter de taper certains arguments

Le fichier de configuration doit être situé à la racine de votre application et doit se nommer `kit-newpage.ini`.

La syntaxe utilisée est celle d'un fichier de configuration php.

Voici les valeurs possibles:

```ini
controllerModelDir = [app]/class-modules/Ekom/Kit/PageCreator/assets
controllerModel = DummyEkomBack
defaultEnv = back
defaultModule = Ekom
controllerDir = Pages
```

- Le controller Model permet d'utiliser les modèles de contrôleur situés dans le dossier (`planets/Kamille/Utils/Console/assets`).
- pour définir un autre dossier, utilisez controllerModelDir. Le tag [app] représente le chemin absolu de l'application


 

Créer une application
----------------

Cette commande permet de créer rapidement une application kamille.

```bash
kamille newapp {appName}
```

Cette commande va en gros importer le modèle d'application situé ici: `https://github.com/lingtalfi/kamille-app`




Importer un module externe
----------------

Cette commande permet d'importer/installer un module dans votre application kamille.


L'importation suivie de l'installation peut se faire avec la seule commande: `install`, comme ceci:

```bash
kamille install {moduleName}
```



Empaqueter son propre module
----------------

Cette commande permet de préparer un module pour l'export.
Le processus est décrit plus en détails sur la page [ModulePacker](tools/kamille-module-packer.md). 


```bash
kamille pack {moduleName}
```



