<?php


use BumbleBee\Autoload\ButineurAutoloader;
use Packer\Packer;


/**
 * That's my private script to generate the packed output (packed.txt at the root of this app).
 * I call this script via a webserver or from the terminal (actually faster)
 */

ini_set("display_errors", "1");

require_once __DIR__ . '/class-planets/BumbleBee/Autoload/BeeAutoloader.php';
require_once __DIR__ . '/class-planets/BumbleBee/Autoload/ButineurAutoloader.php';
ButineurAutoloader::getInst()
    ->addLocation(__DIR__ . "/class-planets")
->start();


$d = __DIR__ . "/../pprivate";
$packer = new Packer();
$c = $packer->addDroppedNamespace("BumbleBee/Autoload")->pack($d);

$script = file_get_contents(__DIR__ . "/assets/kamilletpl.php");
$script = str_replace('//replace', $c, $script);


$destFile = __DIR__ . "/../kamille";
file_put_contents($destFile, $script);

echo "ok" . PHP_EOL;

