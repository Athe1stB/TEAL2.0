<?php

/**
 * @package    local
 * @subpackage teal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     abhiandthetruth, thesmallstar
 */

class ILOTaxonomy
{
    public const ILO_TAXONOMY_DATA_FILE_PATH = __DIR__ . '/../datastore/solo_and_blooms_taxonomy.json';
    public static $ILO_taxonomy;

    private static function load_taxonomy_data()
    {
        $raw_json_string = file_get_contents(self::ILO_TAXONOMY_DATA_FILE_PATH);
        self::$ILO_taxonomy = (array)json_decode($raw_json_string, true);
    }

    public static function get_ILO_levels()
    {
        if (!isset(self::$ILO_taxonomy)) self::load_taxonomy_data();
        return array_keys(self::$ILO_taxonomy);
    }

    public static function get_ILO_verbs()
    {
        if (!isset(self::$ILO_taxonomy)) self::load_taxonomy_data();
        $verbs = [];
        foreach (self::$ILO_taxonomy as $level => $verb_list)
            $verbs = array_merge($verbs, $verb_list);
        return $verbs;
    }

    public static function get_verbs_for_ILO_level($ILO_level)
    {
        if (!isset(self::$ILO_taxonomy)) self::load_taxonomy_data();
        return self::$ILO_taxonomy[$ILO_level];
    }
}
