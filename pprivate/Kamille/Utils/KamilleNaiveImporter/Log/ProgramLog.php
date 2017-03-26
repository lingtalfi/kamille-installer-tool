<?php


namespace Kamille\Utils\KamilleNaiveImporter\Log;

/**
 * Foreground
 * ----------------
 * black: 0;30
 * blue: 0;34
 * green: 0;32
 * cyan: 0;36    (light blue)
 * red: 0;31
 * purple: 0;35
 * brown: 0;33
 * yellow: 1;33
 * light gray: 0;37
 * white: 1;37
 *
 *
 * dark gray: 1;30
 * light blue: 1;34
 * light green: 1;32
 * light cyan: 1;36
 * light red: 1;31
 * light purple: 1;35
 * light gray: 0;37
 *
 *
 * Background
 * ----------------
 * black: 40
 * red: 41
 * green: 42
 * yellow: 43
 * blue: 44
 * magenta: 45
 * cyan: 46
 * light gray: 47
 *
 */
class ProgramLog
{

    private static $dampened = [];


    public static function debug($msg, $lbr = true)
    {
        self::msg('debug', $msg, "0;34", $lbr);
    }

    public static function info($msg, $lbr = true)
    {
        self::msg('info', $msg, "0;30", $lbr);
    }

    public static function warn($msg, $lbr = true)
    {
        self::msg('warn', $msg, "1;31", $lbr);
    }

    public static function error($msg, $lbr = true)
    {
        self::msg('error', $msg, "0;31", $lbr);
    }

    public static function success($msg, $lbr = true)
    {
        self::msg('success', $msg, "0;32", $lbr);
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    public static function setDampened(array $dampened)
    {
        self::$dampened = $dampened;
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    private static function msg($type, $msg, $colorCode, $lbr = true)
    {

        // is dampened?
        if (in_array($type, self::$dampened, true)) {
            return;
        }


        echo "\e[" . $colorCode . "m$msg\e[0m";
        if (true === $lbr) {
            echo PHP_EOL;
        }
    }
}


