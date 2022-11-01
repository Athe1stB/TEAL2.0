<?php

/**
 * @package    local
 * @subpackage teal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     abhiandthetruth, thesmallstar
 */

class CAH3Classifier
{
    public const CAH3_CLASSIFICATION_DATA_FILE_PATH = __DIR__ . '/../datastore/cah3_classification.json';
    public static $cah3_classifications;

    private static function load_classification_data()
    {
        $raw_json_string = file_get_contents(self::CAH3_CLASSIFICATION_DATA_FILE_PATH);
        self::$cah3_classifications = (array)json_decode($raw_json_string, true);
    }

    public static function get_cah3_domains()
    {
        if (!isset(self::$cah3_classifications)) self::load_classification_data();
        return array_keys(self::$cah3_classifications);
    }

    public static function get_cah3_sub_domains()
    {
        if (!isset(self::$cah3_classifications)) self::load_classification_data();
        $sub_domains = [];
        foreach (self::get_cah3_domains() as $domain) {
            $sub_domains = array_merge($sub_domains, array_keys(self::$cah3_classifications[$domain]));
        }
        return $sub_domains;
    }

    public static function get_cah3_skills()
    {
        if (!isset(self::$cah3_classifications)) self::load_classification_data();
        $skills = [];
        foreach (self::$cah3_classifications as $domain => $sub_domain) {
            foreach ($sub_domain as $sub_domain_name => $skills_list)
                $skills = array_merge($skills, $skills_list);
        }
        return $skills;
    }


    public static function get_cah3_sub_domains_from_domain(string $domain)
    {
        if (!isset(self::$cah3_classifications)) self::load_classification_data();
        return array_keys(self::$cah3_classifications[$domain]);
    }

    public static function get_cah3_skills_from_sub_domain(string $domain, string $sub_domain)
    {
        if (!isset(self::$cah3_classifications)) self::load_classification_data();
        return self::$cah3_classifications[$domain][$sub_domain];
    }
}
