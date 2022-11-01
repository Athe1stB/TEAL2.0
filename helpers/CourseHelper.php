<?php

/**
 * @package    local
 * @subpackage teal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     abhiandthetruth, thesmallstar
 */

require_once($CFG->dirroot . '/local/teal/helpers/GlobalDatabase.php');

class CourseHelper
{
    public const COURSE_CODE_LENGTH = 8;
    public const COURSE_CODE_PREFIX = "CRS";
    public const COURSE_LEVELS = ["UG", "PG"];

    public static function get_course_name_from_course_repo_name(string $course_repo_name)
    {
        return str_replace("_", " ", substr($course_repo_name, self::COURSE_CODE_LENGTH));
    }

    public static function get_course_code_from_course_repo_name(string $course_repo_name)
    {
        return substr($course_repo_name, 0, self::COURSE_CODE_LENGTH);
    }

    public static function get_course_repo_names()
    {
        $repo_names = GlobalDatabase::get_repo_names();
        $course_repo_names = [];
        foreach ($repo_names as $repo_name) {
            if (substr($repo_name, 0, strlen(self::COURSE_CODE_PREFIX)) == self::COURSE_CODE_PREFIX)
                array_push($course_repo_names, ($repo_name));
        }
        return $course_repo_names;
    }

    public static function get_local_repo_names()
    {
        $repo_names = LocalDatabase::get_course_records();
        $course_repo_names = [];
        foreach ($repo_names as $repo_name) {
            $course_repo_names[$repo_name->moodle_course_id] = $repo_name->name;
        }
        return $course_repo_names;
    }

    public static function get_course_levels_list()
    {
        return self::COURSE_LEVELS;
    }

    public static function get_course_details_from_repo_name()
    {
    }

    public static function is_present_locally($course_code)
    {
        $course_records = LocalDatabase::get_course_records();
        foreach ($course_records as $course_record) {
            if ($course_code == $course_record->code) return true;
        }
        return false;
    }

    public function get_repo_name_from_course_record($course_record)
    {
        return str_replace(' ', '_', $course_record->code . '_' . $course_record->name);
    }

    public static function generate_course_code_from_course_name($course_name)
    {
        $random_number = mt_rand(100, 999);
        $name_without_space = str_replace(' ', '', $course_name);
        return "CRS" . strval($random_number) . substr($name_without_space, 0, 2); // 'CRS*'
    }

    public static function calculate_total_credits_from_ILOs($ILOs)
    {
        $total_credits = 0;
        if (isset($ILOs)) foreach ($ILOs as $ILO) {
            $ILO = (object)$ILO;
            $total_credits += (int)$ILO->credit;
        }
        return $total_credits;
    }
}
