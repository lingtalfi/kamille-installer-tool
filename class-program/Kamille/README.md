Kamille
===========
2017-03-12 --> 2017-04-02



My first implementation of the [kam framework](https://github.com/lingtalfi/kam).

Kamille is part of the [universe framework](https://github.com/karayabin/universe-snapshot).



Install
=============

```bash
uni import Kamille
```




Getting started
===================

You should start by reading the implementation notes in the **doc** directory.
That's the only doc available so far.

Then, the examples below might get you going.



Example index.php controller
--------------------------------


```php
<?php



use Kamille\Architecture\Application\Web\WebApplication;
use Kamille\Architecture\ApplicationParameters\Web\WebApplicationParameters;
use Kamille\Architecture\Request\Web\HttpRequest;
use Kamille\Architecture\RequestListener\Web\ControllerExecuterRequestListener;
use Kamille\Architecture\RequestListener\Web\ResponseExecuterListener;
use Kamille\Architecture\RequestListener\Web\RouterRequestListener;
use Kamille\Architecture\Router\Web\StaticObjectRouter;
use Services\X;


require_once __DIR__ . "/../init.php";

WebApplicationParameters::boot();





WebApplication::inst()
    ->set('theme', "gentelella")// this application uses a theme
    ->addListener(RouterRequestListener::create()
        ->addRouter(StaticObjectRouter::create()->setUri2Controller(X::getStaticObjectRouter_Uri2Controller()))
//        ->addRouter(StaticPageRouter::create()
//            ->setStaticPageController(X::getStaticPageRouter_StaticPageController())
//            ->setUri2Page(X::getStaticPageRouter_Uri2Page()))
    )
    ->addListener(ControllerExecuterRequestListener::create())
    ->addListener(ResponseExecuterListener::create())
    ->handleRequest(HttpRequest::create());




```



Example MVC code (should be inside a Controller)
--------------------------------

First, create a theme folder containing the following files (find it in this repo in the **doc** directory):

- theme/
    - layout/
        - home.tpl.php 
    - widget/
        - group/
            - group.tpl.php
        - kart/ 
            - kart.tpl.php
        - meteo/ 
            - meteo.tpl.php
            
            
Then, you can use this example code:
            
            
```php
<?php


use Kamille\Ling\Z;
use Kamille\Mvc\Layout\HtmlLayout;
use Kamille\Mvc\Loader\FileLoader;
use Kamille\Mvc\Renderer\PhpLayoutRenderer;
use Kamille\Mvc\Widget\GroupWidget;
use Kamille\Mvc\Widget\Widget;

require_once __DIR__ . "/../init.php";


$wloader = FileLoader::create()->addDir(Z::appDir() . "/theme/widget");
$commonRenderer = PhpLayoutRenderer::create();


//HtmlPageHelper::$title = "Coucou";
//HtmlPageHelper::$description = "Ca va ?";
//HtmlPageHelper::css("/styles/style.css");
//HtmlPageHelper::js("/js/lib/mylib.js", "jquery", ["defer" => "true"]);
//HtmlPageHelper::js("/js/poite/poire.js");
//HtmlPageHelper::addBodyClass("marsh");
//HtmlPageHelper::addBodyClass("mallow");
//HtmlPageHelper::addBodyAttribute("onload", "tamere");
//HtmlPageHelper::js("/js/lib/sarah", null, null, false);


echo HtmlLayout::create()
    ->setTemplate("home")
    ->setLoader(FileLoader::create()
        ->addDir(Z::appDir() . "/theme/layout")
    )
    ->setRenderer($commonRenderer)
    ->bindWidget("group", GroupWidget::create()
        ->setTemplate("group/group")
        ->setLoader($wloader)
        ->setRenderer($commonRenderer)
        ->bindWidget("meteo", Widget::create()
            ->setTemplate("meteo/meteo")
            ->setVariables(['level' => "good"])
            ->setLoader($wloader)
            ->setRenderer($commonRenderer)
        )
        ->bindWidget("kart", Widget::create()
            ->setTemplate("kart/kart")
            ->setLoader($wloader)
            ->setRenderer($commonRenderer)
        )
    )
    ->render([
        "name" => 'Pierre',
    ]);



```            




History Log
===============
    
- 1.33.0 -- 2017-04-10

    - ModuleInstaller moved to Module subdirectory 
    
- 1.32.0 -- 2017-04-10

    - add reuse argument to the AbstractX.get method 
    
- 1.31.0 -- 2017-04-09

    - WebApplication now has the lang parameter 
    
- 1.30.0 -- 2017-04-09

    - XConfig now uses dot notation to access deep array levels 
    - Z::uri method now works without request 
    
- 1.29.0 -- 2017-04-08

    - add Z::uri method 
    
- 1.28.0 -- 2017-04-07

    - fix LawsUtil default Widget class 
    
- 1.27.0 -- 2017-04-07

    - update LawsUtil arguments 
    
- 1.26.0 -- 2017-04-06

    - fix LawsLayoutProxy.includes 
    - created PublicWidgetInterface 
    
- 1.25.0 -- 2017-04-06

    - add LawsLayoutProxy.includes method
    
- 1.24.0 -- 2017-04-04

    - fix ModuleInstallTool problem with unbind hooks
    
- 1.23.0 -- 2017-04-02

    - add laws implementation
    
- 1.22.0 -- 2017-04-02

    - integrated ex-KaminosUtils class
    
- 1.21.0 -- 2017-04-02

    - add RouterHelper
    
- 1.20.0 -- 2017-04-02

    - add Z.requestParam
    
- 1.19.0 -- 2017-04-02

    - add FakeHttpRequest
    
- 1.18.0 -- 2017-03-29

    - add WidgetInstallerInterface
    
- 1.17.0 -- 2017-03-28

    - add LawsLayoutProxy
    
- 1.16.0 -- 2017-03-27

    - add param to the Hook.call method
    - add StaticObjectRouter.setDefaultController
    
- 1.15.0 -- 2017-03-26

    - added KamilleNaiveImporter
    
- 1.14.0 -- 2017-03-25

    - added XInstalledModules
    
- 1.13.0 -- 2017-03-21

    - update StepTrackerAwareModule
    
- 1.12.0 -- 2017-03-21

    - added StepTrackerAwareInterface.getStepTracker
    
- 1.11.0 -- 2017-03-21

    - removed WebApplicationParameters (moved to app specific like kaminos)
    
- 1.10.0 -- 2017-03-20

    - added AbstractX and AbstractXConfig
    
- 1.9.0 -- 2017-03-20

    - added ModuleInstaller and StepTracker
    
- 1.8.0 -- 2017-03-17

    - moved application parameters outside the Application
    
- 1.7.0 -- 2017-03-16

    - add ControllerExecuterRequestListener.throwExOnControllerNotFound method
    
- 1.6.0 -- 2017-03-16

    - add possibility of choosing the method from the StaticObjectRouter's controller string
    
    
- 1.5.0 -- 2017-03-16

    - Remove built-in dependencies to X and Hooks
    
- 1.4.0 -- 2017-03-16

    - Undo add WebControllerInterface
    
- 1.3.0 -- 2017-03-16

    - Added WebControllerInterface 
    
- 1.2.0 -- 2017-03-15

    - LayoutProxy catches Exception on widget rendering 
    
- 1.1.0 -- 2017-03-13

    - now PhpLayoutRenderer passes the v variable as an array instead of an object 

- 1.0.0 -- 2017-03-12

    - initial commit



