<?php


use BumbleBee\Autoload\ButineurAutoloader;
use Packer\Packer;


ini_set("display_errors", "1");

require_once __DIR__ . '/class-planets/BumbleBee/Autoload/BeeAutoloader.php';
require_once __DIR__ . '/class-planets/BumbleBee/Autoload/ButineurAutoloader.php';
ButineurAutoloader::getInst()
    ->addLocation(__DIR__ . "/class-planets")
->start();


$d = __DIR__ . "/../pprivate";
$packer = new Packer();
$c = $packer->addDroppedNamespace("BumbleBee/Autoload")->pack($d);

$destFile = __DIR__ . "/../packed.txt";
file_put_contents($destFile, $c);

echo "ok";





