<?php


namespace KamilleNaiveImporter\Importer;


use Bat\FileSystemTool;
use KamilleNaiveImporter\ImportSummary\ImportSummary;
use KamilleNaiveImporter\KamilleNaiveImporter;

class LingUniverseImporter implements UniverseImporterInterface, KamilleNaiveImporterAwareImporterInterface
{

    private static $dependencyMap = [
        "AdminTable" => ["QuickPdo"],
        "ApplicationLog" => ["Bat"],
        "ArrayExport" => ["ArrayToString"],
        "ArrayStore" => ["ArrayExport", "Bat"],
        "ArrayToString" => [],
        "ArrayToTable" => ["Bat"],
        "AssetLoader" => [],
        "AssetsList" => ["Bat"],
        "BabyDash" => ["IndentedLines"],
        "BabyYaml" => ["Bat"],
        "Bat" => ["CopyDir", "Tiphaine"],
        "Bate" => ["Bat"],
        "Beauty" => ["DirScanner"],
        "BullSheet" => ["Bat", "DirScanner", "QuickPdo"],
        "BumbleBee" => [],
        "Colis" => ["Bat", "YouTubeUtils", "Tim", "UploaderHandler"],
        "CommandLineManiac" => [],
        "ConventionGuy" => [],
        "CopyDir" => [],
        "Csv" => [],
        "DirScanner" => ["Bat"],
        "DirTransformer" => ["Bat"],
        "Dreamer" => [],
        "Escaper" => ["Bat"],
        "Explorer" => [],
        "Ffmpeg" => [],
        "FileCleaner" => [],
        "FileCreator" => [],
        "GetFileSize" => [],
        "Here" => [],
        "HtmlTemplate" => [],
        "Icons" => [],
        "IndentedLines" => ["Bat", "Bate", "Quoter"],
        "Installer" => ["Bat"],
        "InstantLog" => ["Bat"],
        "JAjaxLoader" => [],
        "JChronometer" => [],
        "JCookie" => [],
        "JDragSlider" => [],
        "JFullScreen" => [],
        "JGoodies" => [],
        "JImageRotator" => [],
        "JInfiniteSlider" => [],
        "JItemSlider" => [],
        "JQuery" => [],
        "JVideoPlayer" => [],
        "JqueryUrlWithDropZone" => ["Jquery"],
        "Kamille" => [],
        "KamilleWidgets" => ["Kamille"],
        "Linker" => [],
        "LogSlicer" => ["Bat"],
        "Lys" => [],
        "Meredith" => ["Bat", "QuickPdo", "Tim", "StringFormatter", "SuspiciousException"],
        "MikeMagicTools" => [],
        "MySimpleXmlElement" => [],
        "MysqlTabular" => [],
        "NotationFan" => [],
        "Observer" => [],
        "Ornella" => [],
        "Pea" => [],
        "PermsHiker" => ["Bat", "DirScanner"],
        "PhpBeast" => ["ArrayToTable"],
        "PhpTemplate" => [],
        "Privilege" => [],
        "PublicException" => [],
        "QuickForm" => ["Bat", "QuickPdo"],
        "QuickLog" => ["Bat"],
        "QuickPdo" => [],
        "Quoter" => ["Bat", "WrappedString", "Escaper"],
        "ReflectionRepresentation" => ["VariableToString"],
        "RssUtil" => ["MySimpleXmlElement"],
        "ScreenDebug" => [],
        "SecureImageUploader" => ["Bat", "ThumbnailTools"],
        "SelectChain" => ["Tim"],
        "SequenceMatcher" => [],
        "SitemapBuilderBox" => ["Bat"],
        "SitemapSlicer" => ["Bat", "SitemapBuilderBox"],
        "StringFormatter" => ["ArrayToString", "VariableToString"],
        "SuspiciousException" => [],
        "SvgGridGenerator" => [],
        "TheAnarchist" => [],
        "TheBar" => [],
        "TheScientist" => [],
        "ThumbnailTools" => ["Bat"],
        "Tim" => ["Jquery"],
        "TimeFileUtil" => [],
        "Tiphaine" => [],
        "TokenFun" => ["Bat", "DirScanner"],
        "Tokens" => [],
        "TreeListHelper" => ["Bat"],
        "Umail" => ["DirScanner"],
        "UniqueNameGenerator" => ["Bat"],
        "Updf" => [], // requires https://github.com/tecnickcom/tcpdf
        "Uploader" => [],
        "UploaderHandler" => [],
        "UrlFriendlyListHelper" => ["Bat", "Jquery", "QuickPdo"],
        "VSwitch" => [],
        "VariableToString" => ["ArrayToString", "ReflectionRepresentation"],
        "VideoSubtitles" => [],
        "WrappedString" => ["Escaper"],
        "YouTubeUtils" => [],
        "Zoli" => [],


    ];


    private $_forceImport;

    public function __construct()
    {
        $this->_forceImport = false;
    }


    public static function create()
    {
        return new static();
    }



    public function getName()
    {
        return "LingUniverseImporter";
    }

    public function getAvailablePlanets($universe = null)
    {

        $ret = [];
        if (null === $universe || "ling" === $universe) {
            $planets = array_keys(self::$dependencyMap);
            foreach ($planets as $planet) {
                $ret[] = [$planet, "ling"];
            }
        }
        return $ret;
    }


    public function forceImport($forceImport)
    {
        $this->_forceImport = $forceImport;
        return $this;
    }


    public function canImport($planetName, $universe = null)
    {
        if (null === $universe || 'ling' == $universe) {
            return array_key_exists($planetName, self::$dependencyMap);
        } else {
            return false;
        }
    }

    public function import($planetsDir, $planets)
    {
        $success = false;
        $summary = ImportSummary::create();
        if (is_dir($planetsDir)) {
            $success = true;


            $tree = [];
            $this->collectDependencyTree($planets, $tree);
            $tree = array_unique($tree);


            foreach ($tree as $planet) {
                $output = [];
                $returnVar = 0;
                if (file_exists($planetsDir . "/" . $planet)) {
                    if (true === $this->_forceImport) {
                        FileSystemTool::remove($planetsDir . "/" . $planet);
                    } else {
                        $summary->addAlreadyInstalledPlanet($planet);
                        continue;
                    }
                }

                $cmd = 'cd "' . $planetsDir . '"; git clone https://github.com/lingtalfi/' . $planet . '.git';
                exec($cmd, $output, $returnVar);
                if (0 === $returnVar) {
                    $summary->addReinstalledPlanet($planet);
                } else {
                    $success = false;
                    $summary->addUninstalledPlanet($planet);
                }
            }
        }
        $summary->setSuccessful($success);
        return $summary;
    }


    public function setKamilleNaiveImporter(KamilleNaiveImporter $uni)
    {
        // TODO: Implement setKamilleNaiveImporter() method.
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    private function collectDependencyTree($planets, array &$tree)
    {
        if (is_string($planets)) {
            $planetName = $planets;
            $tree[] = $planetName;
            if (array_key_exists($planetName, self::$dependencyMap)) {
                $deps = self::$dependencyMap[$planetName];
                foreach ($deps as $dep) {
                    if (!in_array($dep, $tree, true)) {
                        self::collectDependencyTree($dep, $tree);
                    }
                }
            }
        } elseif (is_array($planets)) {
            foreach ($planets as $planetName) {
                $this->collectDependencyTree($planetName, $tree);
            }
        }
    }
}