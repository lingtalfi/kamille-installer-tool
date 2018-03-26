<?php


namespace Kamille\Utils\Morphic\Generator2\Helper;


use QuickPdo\QuickPdoInfoTool;

class LingFrenchMorphicGeneratorHelper
{

    /**
     * Note: dump in your browser and copy/paste the source code of
     * the html page...
     */
    public static function dumpTableBluePrint($prefix, $db = null)
    {
        if (null === $db) {
            $db = QuickPdoInfoTool::getDatabase();
        }
        $tables = QuickPdoInfoTool::getTables($db, $prefix);

        $s = <<<EEE
<?xml version='1.0' standalone='yes'?>
<infos>
EEE;
        $s .= PHP_EOL;

        foreach ($tables as $table) {
            $s .= <<<EEE
    <item table="$table">
        <label>$table</label>
        <labelPlural>$table</labelPlural>
        <genre>f</genre>
        <article>la</article>
    </item>
EEE;
            $s .= PHP_EOL;
        }
        $s .= <<<EEE
</infos>
EEE;
        $s .= PHP_EOL;

        echo $s;
    }


    /**
     * Note: dump in your browser and copy/paste the source code of
     * the html page...
     */
    public static function dumpColsBluePrint($prefix, $db = null)
    {
        if (null === $db) {
            $db = QuickPdoInfoTool::getDatabase();
        }

        $tables = QuickPdoInfoTool::getTables($db, $prefix);

        $s = <<<EEE
<?xml version='1.0' standalone='yes'?>
<cols>
EEE;
        $s .= PHP_EOL;


        $allCols = [];
        foreach ($tables as $table) {
            $cols = QuickPdoInfoTool::getColumnNames($table, $db);
            $allCols = array_merge($allCols, $cols);
        }

        $allCols = array_unique($allCols);
        sort($allCols);

        foreach ($allCols as $col) {
            $label = str_replace('_', ' ', $col);
            $s .= <<<EEE
    <item>
        <name>$col</name>
        <value>$label</value>
    </item>
EEE;
            $s .= PHP_EOL;
        }
        $s .= <<<EEE
</cols>
EEE;
        $s .= PHP_EOL;

        echo $s;
    }
}