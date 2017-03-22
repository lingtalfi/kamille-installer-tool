<?php


namespace KamilleNaiveImporter\Log;


class ProgramLog
{


    public static function debug($msg)
    {
        a("debug: $msg");
    }

    public static function info($msg)
    {
        a("info: $msg");
    }

    public static function warn($msg)
    {
        a("warn: $msg");
    }

    public static function error($msg)
    {
        a("error: $msg");
    }

    public static function success($msg)
    {
        a("success: $msg");
    }
}


