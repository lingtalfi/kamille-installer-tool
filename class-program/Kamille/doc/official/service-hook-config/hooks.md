Hooks
==========
2018-03-02



Les Hooks jouent un rôle primordial dans une application kamille:
ils permettent aux modules de communiquer entre eux et avec le reste de l'application.

L'utilisation d'un hook implique 3 parties:

- déclaration d'un hook
- inscription à un hook
- appel d'un hook



Déclaration d'un hook
------------------------

Les hooks sont déclarés ici: **class-core/Services/Hooks.php**.

Par convention, un hook est nommé d'après son module.

- nomDuHook: $NomModule_$nomDuHook


Par exemple, tous les hooks déclarés par le module Core commencent par **Core_**.


Pour déclarer un hook, il faut créer une méthode vide **protected static** dans le fichier Hooks. 

Exemple:

```php

    protected static function Core_Controller_onControllerStringReceived(&$controllerString)
    {
    
    }
```


Il est possible de passer le premier argument (et le premier argument seulement) d'un hook par référence, 
en utilisant l'esperluette (&) devant le nom du paramètre, comme c'est le cas dans l'exemple ci-dessus. 





Inscription à un hook
------------------------

Pour s'inscrire à un hook, il suffit d'ajouter le code que l'on souhaite directement dans le hook déclaré précédemment.


```php

    protected static function Core_Controller_onControllerStringReceived(&$controllerString)
    {
        // mit-start:MyModule
        $controllerString = 6;         
        // mit-end:MyModule         
    }
```

Il est IMPÉRATIF d'encadrer son code par les deux commentaires contenant le nom du module:

```php
// mit-start:MyModule         
// mit-end:MyModule         
```

Cet encadrement:

- permet d'y voir plus clair dans son propre code
- est utilisée par l'[installateur de modules](/modules?id=l39installateur-de-modules) pour supprimer, inscrire les hooks des modules étant supprimés ou installés







Appel d'un hook
------------------------

Pour appeler un hook depuis le code de l'application, on utilise la méthode call de l'objet Hooks:


```php
<?php 
use Core\Services\Hooks;


$controllerString = "";
Hooks::call("Core_Controller_onControllerStringReceived", $controllerString);
```