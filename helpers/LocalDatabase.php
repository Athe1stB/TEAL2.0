<?php

/**
 * @package    local
 * @subpackage teal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     abhiandthetruth, thesmallstar
 */

require_once($CFG->dirroot . '/local/teal/vendor/autoload.php');

class LocalDatabase
{
    private static $settings;

    /*
        Settings contain the following
        - github_access_token   : token of the user account connected with current teal instance
        - org_global            : name of the organisation under which data is organised
    */
    public static function getTealSettings()
    {
        if (!isset(self::$settings)) {
            global $DB;
            $settings_array = $DB->get_records('teal_settings');
            self::$settings = [];
            foreach ($settings_array as $setting) {
                self::$settings[$setting->name] = $setting->value;
            }
        }
        return self::$settings;
    }

    public static function update_teal_settings_from_form(object $form_response)
    {
        global $DB;
        foreach ($form_response as $id => $value) {
            if ($id !== "submitbutton")
                $DB->update_record('teal_settings', (object)["id" => $id, "value" => $value]);
        }
    }

    public static function get_course_record_by_code(string $code)
    {
        global $DB;
        $course = $DB->get_record('teal_course_metadata', ["code" => $code]);
        return $course;
    }

    public static function get_course_record_by_id(string $id)
    {
        global $DB;
        $course = $DB->get_record('teal_course_metadata', ["id" => $id]);
        return $course;
    }

    public static function get_course_records()
    {
        global $DB;
        $courses = $DB->get_records('teal_course_metadata');
        return $courses;
    }

    public static function get_program_records()
    {
        global $DB;
        $programs = $DB->get_records('teal_program_metadata');
        return $programs;
    }

    public static function get_program_record_by_id(string $id)
    {
        global $DB;
        $program = $DB->get_record('teal_program_metadata', ["id" => $id]);
        return $program;
    }

    public static function get_program_record_by_code(string $code)
    {
        global $DB;
        $program = $DB->get_record('teal_program_metadata', ["code" => $code]);
        return $program;
    }

    public static function insert_course_record(object $course_record)
    {
        global $DB;
        $id = $DB->insert_record('teal_course_metadata', $course_record);
        return $id;
    }

    public static function update_course_record(object $course_record)
    {
        global $DB;
        $DB->update_record('teal_course_metadata', $course_record);
    }

    public static function insert_program_record(object $program_record)
    {
        global $DB;
        $id = $DB->insert_record('teal_program_metadata', $program_record);
        return $id;
    }

    public static function update_program_record(object $program_record)
    {
        global $DB;
        $DB->update_record('teal_program_metadata', $program_record);
    }
}
