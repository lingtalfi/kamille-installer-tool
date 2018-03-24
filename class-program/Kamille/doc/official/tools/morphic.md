Morphic
===========
2018-03-12


!> Page nécessitant une relecture approfondie


Morphic est un système de gestion de formulaires/listes.




Introduction
------------

A la base, c'était juste une couche javascript permettant de transformer une table html inanimée
en une liste d'éléments interactive (que l'on utilise dans les back-offices).

La documentation originale (première version) de morphic se trouve ici: `planets/Kamille/doc/morphic/morphic-notes.md`.

<img src="image/morphic-table.png" alt="Drawing"/>


Principes de base
-------------------

Morphic fonctionne en appelant des fichiers de configuration php.
Chaque formulaire a son propre fichier de configuration, et chaque liste a également son propre fichier de configuration.


Les avantages d'utiliser des fichiers de configuration php sont les suivants:

- ils sont facilement modifiables par le développeur
- on peut étendre les fonctionnalités à l'infini en rajoutant les propriétés que l'on veut
- on peut générer automatiquement des fichiers de configuration de base (auto-admin)


Les contrôleurs
------------------

Dans une application MVC comme kamille, le point de départ est le contrôleur.
Le framework kamille propose deux méthodes pour invoquer ces fichiers de configuration;

#### pour les listes

```php
$listConfig = A::getMorphicListConfig($module, $identifier, array $context=[]);
```

$listConfig est un tableau php pouvant être passé directement à la vue.

Exemple concret: `class-controllers/Ekom/Back/Catalog/ProductController.php`






#### pour les formulaires

```php
$formConfig = A::getMorphicFormConfig('Ekom', $form, $context);
$this->handleMorphicForm($formConfig);
```


$formConfig est un tableau php pouvant être passé directement à la vue.

Exemple concret: `class-controllers/Ekom/Back/Catalog/ProductController.php`




!> Les fichiers de configuration permettent de contrôler les **éléments** formulaires et listes.
Ces **éléments** sont encapsulés dans un **widget** qui est géré par le contrôleur.
Exemple concret: `class-controllers/Ekom/Back/Catalog/ProductController.php`




#### Un contrôleur standard

Bien qu'il n'y ait pas encore de contrôleur standard pour gérer les éléments morphic (le débat est ouvert),
le module Ekom propose une approche intéressante permettant de gérer les widgets des deux 
types (formulaires et listes) facilement.
Ce modèle pourrait bien devenir le standard dans un futur proche.

Le modèle en question: `class-controllers/Ekom/Back/Pattern/EkomBackSimpleFormListController.php`, est utilisé par exemple par
`class-controllers/Ekom/Back/Catalog/ProductController.php`.  




#### Configuration d'un contrôleur


Certains contrôleurs, par le biais de l'héritage de classes, peuvent mettre à disposition des méthodes
de configuration pratiques pour le développeur.


Voici un exemple de contrôleur fourni par le module [ekom](https://github.com/KamilleModules/Ekom) (module de e-commerce pour kamille)
qui étend un contrôleur généré par l'outil morphic, et surcharge certaines valeurs de configuration.

```php
<?php

namespace Controller\Ekom\Back\Catalog;


use Controller\Ekom\Back\Generated\EkProductHasTag\EkProductHasTagListController;

class ProductHasTagListController extends EkProductHasTagListController
{
    public function __construct()
    {
        parent::__construct();
        $this->addConfigValues([
            'route' => "Ekom_Catalog_ProductHasTag_List",
            'form' => "back/catalog/product_has_tag",
            'list' => "back/catalog/product_has_tag",
            /**
             * This defines the selected menu item (left menu of nullos)
             * when you render a formList using the "withParent" method WITHOUT
             * specifying the ric in the uri
             */
            'parent2Route' => [
                'ek_product' => 'Ekom_Generated_EkProduct_List',
                'ek_tag' => 'Ekom_Catalog_Tag_List',
            ],
            /**
             * This defines the selected menu item (left menu of nullos)
             * when you render a formList using the "withNoParent" method WITH
             * specifying the ric in the uri
             */
            "menuCurrentRoute" => "Ekom_Catalog_Tag_List",
        ]);
    }
}

```








Fichier de configuration: Formulaire
----------------------------------

Exemple: `config/morphic/Ekom/back/utils/cache_manager.form.conf.php`

Voici les propriétés disponibles pour le fichier de configuration des formulaires morphic:

- `title`: le titre du formulaire 
- `description`: une description  
- `form`: l'instance SokoFormInterface  
- `submitBtnLabel`: le label du bouton submit  
- `feed`: la fonction à appeler pour pré-remplir le form en mode update (voir `Controller\Ekom\Back\EkomBackController::handleMorphicForm` pour plus de détails)  
- `process`: la fonction à appeler lorsque les valeurs du formulaire sont remplies (voir `Controller\Ekom\Back\EkomBackController::handleMorphicForm` pour plus de détails)  
- `ric`: les ric pour ce formulaire  
- `formAfterElements`: tableau pour ajouter des éléments supplémentaires comme les liens-pivots par exemple (voir exemple dans `config/morphic/Ekom/back/utils/cache_manager.form.conf.php`).



Fichier de configuration: Liste
----------------------------------

Exemple: `config/morphic/Ekom/back/catalog/card.list.conf.php`

Voici les propriétés disponibles pour le fichier de configuration des listes morphic:


- `title`: le titre de la liste
- `table`: une référence de la table. Est utilisée par l'ajax service back.morphic (`service/NullosAdmin/ecp/api.php`)  
- `object`: une référence vers un objet [xiao](https://github.com/lingtalfi/XiaoApi) qui vous permet de gérer des hooks supplémentaires. Est utilisée par l'ajax service back.morphic (`service/NullosAdmin/ecp/api.php`)
Si vous définissez `object`, alors object sera utilisé à la place de table.  
- `viewId`: optionnel, l'identifiant de la liste (par exemple: back/catalog/product). Il est transmis via ajax par la couche morphic.js de manière à synchroniser la liste
générée par ajax et celle générée statiquement. Il est conseillé de ne pas renseigner cette valeur, et de laisser le système utiliser sa valeur par défaut.  
- `headers`: les champs à afficher. Tableau de `column` => label. La dernière colonne spéciale est: `_action => ''` si vous utilisez les actions.   
- `headersVisibility`: les colonnes à masquer. `column` => bool  
- `realColumnMap`: permet de rectifier les fonctions de tri/recherche. Tableau de `column` => `queryRealCol`, queryRealCol étant le nom tel qu'utilisé dans la requête sql (exemple: pcl.product_card_id)  
- `having`: tableau des colonnes qui sont utilisées dans la clause having (plutôt que where). Cela est particulièrement pour le filtrage des données  
- `querySkeleton`: la structure de la requête, en remplaçant les colonnes par `%s` (exemple: `select %s from my_table`)  
- `queryCols`: les `columns` à intégrer dans le querySkeleton; l'ensemble de la syntaxe mysql est possible (as, concat, if, ...)  
- `context`: un ensemble de variables arbitraires passées par le contrôleur. Notez que le service ajax back.morphic les recevra également.
- `deadCols`: un tableau de `column` qui n'auront pas de tri ni de filtre (par exemple pour les images) 
- `colSizes`: un tableau de `column` => largeur (en pixel) 
- `colTransformers`: un tableau de `column` => callback permettant de transformer les colonnes. 
        callback ( columnValue, array row )

- `formRoute`: la route du lien vers le formulaire correspondant. Ce mécanisme est utilisé dans la rowAction "update" par défaut 
- `formRouteUseRic`: bool=true, doit-on utiliser les valeurs ric dans la génération de la rowAction "update" par défaut
- `formRouteExtraVars`: des paramètres supplémentaires (clé => valeur) à ajouter au lien généré avec la propriété `formRoute`.
On peut utiliser les valeurs de row en préfixant la valeur par le symbole $. Exemple: test => $product_id est transformé en test => $row[product_id] 
- `rowActionUpdateRicAdaptor`: un adaptateur (map) permettant de modifier les colonnes définies dans ric en d'autres champs pour ce qui concerne la génération du lien pour la rowAction "update" par défaut 
- `formRouteExtraActions`: un tableau d'actions supplémentaires à ajouter aux rowActions par défaut (même stucture que `rowActions`)   
- `rowActions`: laisser vide pour utiliser les actions par défaut. Un tableau d'action.
    - `name`: le nom symbolique de l'action (ex: update)             
    - `label`: le label (exemple: Modifier)             
    - `icon`: ex fa fa-pencil             
    - `link`: le lien             
    - `?ecp`: pour appeler un service ecp en background, à utiliser **à la place** de link. La chaîne ecp correspond à l'argument target dont la syntaxe est la suivante: &lt;moduleName> &lt;:> &lt;serviceIdentifier>.
    Il est possible de passer des arguments avec la propriété `args`.            
    - `?ecpAfter`: fonctionne seulement si la propriété `ecp` est positionnée. L'action javascript à appeler après que la requête ecp se soit exécutée avec succès. Les valeurs possibles sont:
        - `reload`: recharge la page               
    - `?args`: un tableau (clé => valeur) d'arguments disponible lorsque la propriété `ecp` est positionnée.
    Si la valeur est préfixée par le symbole $ et qu'elle correspond à une clé de ric, alors la valeur de ric correspondante sera envoyée  
    - `?confirm`: le texte de confirmation si c'est une action qui nécessite une confirmation             
    - `?confirmTitle`: le titre du dialogue de confirmation             
    - `?confirmOkBtn`: le texte de bouton validant la demande de confirmation             
    - `?confirmCancelBtn`: le texte de bouton annulant la demande de confirmation
    
La vue
-----------------------

Le template adopté pour l'instant par morphic est le suivant, fourni par le module [NullosAdmin](https://github.com/KamilleModules/NullosAdmin): `theme/nullosAdmin/widgets/Ekom/Main/FormList/default.tpl.php`.

Ce template utilise des objets de rendu (Renderer):

- pour les formulaires: `class-modules/NullosAdmin/SokoForm/Renderer/NullosMorphicBootstrapFormRenderer.php`
- pour les listes: 
    - Renderer du widget liste: `class-themes/NullosAdmin/Ekom/Back/GuiAdminTableRenderer/GuiAdminTableWidgetRenderer.php`
    - Renderer de l'élement liste: `planets/GuiAdminTable/Renderer/MorphicBootstrap3GuiAdminHtmlTableRenderer.php`




    
La couche javascript
-----------------------   

Le script original javascript, codant principalement pour les listes, peut être trouvé ici: `www/theme/nullosAdmin/js/morphic.js`.
    
    
Le générateur
------------------

Morphic propose un outil de génération automatique des fichiers de configuration (formulaires et listes) à partir
d'une base de données.

Cet outil analyse la structure de la base de données et construit une administration de bas niveau en quelques secondes.
Le gain de temps est énorme :)

Voici le code qui a été utilisé pour générer [administration générée](http://www.ling-docs.ovh/ekom/#/user/back/generated-admin)
du module [Ekom](https://github.com/KamilleModules/Ekom): 
 



```php
<?php


use Core\Services\A;
use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
use Module\Ekom\Morphic\Generator\EkomNullosMorphicGenerator2;
use QuickPdo\QuickPdoInfoTool;

// using kamille framework here (https://github.com/lingtalfi/kamille)
require_once __DIR__ . "/../boot.php";
require_once __DIR__ . "/../init.php";


A::testInit();

$app = ApplicationParameters::get("app_dir");

$conf = [
    'tablesWithoutPrefix' => [
        "nested_category",
    ],
    'formControlTypes' => [
        /**
         * autocomplete: in ecp.api.php, search for "auto."...
         */
        "autocomplete" => [
            'ek_' => [
                "address_id" => true,
                "category_id" => true,
                "discount_id" => true,
                "product_id" => true,
                "product_card_id" => true,
                "tag_id" => true,
                "user_id" => true,
            ],
        ],
    ],
];


$allTables = QuickPdoInfoTool::getTables("kamille", null);
$tables = ["ek_user"];
$tables = ["TABLE 69"];
$tables = ["nested_category"];
$tables = ["ek_shop_has_product_has_provider"];
$tables = ["ekev_event_has_course"];
$tables = ["ek_shop_has_product"];
$tables = ["di_user_has_element"];
$tables = ["ek_user_has_product"];
$tables = $allTables;


$morphic = EkomNullosMorphicGenerator2::create()
    ->debug(true)
//    ->recreateCache(true)
    ->setConfiguration($conf)
    ->setControllerBaseDir($app . "/class-controllers/Ekom/Back/Generated")
    ->setListConfigFileBaseDir($app . "/config/morphic/Ekom/generated")
    ->setFormConfigFileBaseDir($app . "/config/morphic/Ekom/generated")
    ->setTables($tables);

$morphic->generate();



```

    