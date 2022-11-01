<?php

/**
 * @package    local
 * @subpackage teal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     abhiandthetruth, thesmallstar
 */

use core_reportbuilder\local\filters\boolean_select;

require_once($CFG->dirroot . '/config.php');
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
require_once($CFG->dirroot . '/backup/converter/convertlib.php');
require_once($CFG->dirroot . '/local/teal/helpers/LocalDatabase.php');
require_once($CFG->dirroot . '/local/teal/helpers/GlobalDatabase.php');
require_once($CFG->dirroot . '/local/teal/helpers/ProgramHelper.php');
require_once($CFG->dirroot . '/local/teal/vendor/autoload.php');
require_once($CFG->dirroot . '/local/teal/models/Course.php');

class Program
{
    // ID used in the local Program metadata table
    // Initialized only after first local commit
    public $id;

    // All the fields of the program
    // Initialized every time object instantiates
    public $name;
    public $code;
    public $objective;
    public $level;
    public $total_credits;
    public $ILOs;
    /* Array of course information containing
    *  repo_name: name of repo containing course information 
    *  commit   : sha of the commit for which course info was pulled
    */
    public $courses;

    /********* Constructors *********/

    public function __construct(
        string $name,
        string $objective,
        string $level,
        $total_credits,
        $ILOs,
        $course_names,
        $code = null
    ) {
        $this->name = $name;
        $this->level = $level;
        $this->objective = $objective;
        $this->code = $code == null ?
            $this->generate_code_from_program_name($name) : $code;
        $this->ILOs = $ILOs;
        $this->total_credits = $total_credits;
        $this->courses = $course_names;
    }

    public static function by_form_data($form_data)
    {
        $courses = [];
        foreach ($form_data->course_ids as $course_id)
            array_push($courses, Course::by_id($course_id));
        $ILOs = [];
        $total_credits = 0;
        $courses_info = [];
        foreach ($courses as $course) {
            $total_credits += $course->total_credits;
            $course_info = new stdClass();
            $course_info->repo_name = $course->get_repo_name();
            $course_info->commit = $course->commit;
            array_push($courses_info, $course_info);
            array_push($ILOs, $course->ILOs);
        }
        $instance = new self(
            $form_data->name,
            $form_data->objective,
            $form_data->level,
            $total_credits,
            $ILOs,
            $courses_info
        );
        return $instance;
    }

    public static function by_id(string $id)
    {
        $program_record = LocalDatabase::get_program_record_by_id($id);
        $instance = new self(
            $program_record->name,
            $program_record->objective,
            $program_record->level,
            $program_record->total_credits,
            json_decode($program_record->ilos),
            json_decode($program_record->courses),
            $program_record->code
        );
        $instance->id = $program_record->id;
        return $instance;
    }

    public static function by_repo_name(string $repo_name)
    {
        $program_record = (object)ProgramHelper::get_program_details_from_repo_name($repo_name);
        $instance = new self(
            $program_record->name,
            $program_record->objective,
            $program_record->level,
            $program_record->total_credits,
            $program_record->ILOs,
            $program_record->courses,
            $program_record->code
        );
        $instance->id = $program_record->id;
        return $instance;
    }

    /********* Logic Functions *********/

    public static function generate_code_from_program_name($program_name)
    {
        $random_number = mt_rand(100, 999);
        $name_without_space = str_replace(' ', '', $program_name);
        return "PGM" . strval($random_number) . substr($name_without_space, 0, 2);
    }

    public function get_repo_name()
    {
        return $this->code . '_' . str_replace(' ', '_', $this->name);
    }

    public function get_github_url()
    {
        return "https://github.com/" . LocalDatabase::getTealSettings()['org_global'] . "/" . $this->get_repo_name();
    }

    public function import_courses()
    {
        foreach ($this->courses as $course_info) {
            $course_info = (object)$course_info;
            if (CourseHelper::is_present_locally(
                CourseHelper::get_course_code_from_course_repo_name(
                    $course_info->repo_name
                )
            ))
                continue;
            $course = Course::by_repo_details(
                $course_info->repo_name,
                $course_info->commit
            );
            $course->create_moodle_course();
            $course->commit_to_local();
            $course->commit_to_global("First commit");
        }
    }

    public function commit_to_local()
    {
        $program_record = (object)(array)$this;
        $program_record->ilos = json_encode($this->ILOs);
        $program_record->courses = json_encode($this->courses);
        $this->import_courses();
        if (!isset($this->id)) LocalDatabase::insert_program_record($program_record);
        else LocalDatabase::update_course_record($program_record);
    }

    public function commit_to_global($commit_message)
    {
        GlobalDatabase::commit_file_on_repo(
            $this->get_course_metadata_file_as_array(),
            $this->get_repo_name(),
            $commit_message
        );
    }

    private function get_course_metadata_file_as_array()
    {
        $content = [
            'name' => $this->name,
            'code' => $this->code,
            'objective' => $this->objective,
            'courses' => $this->courses,
            'ILOs' => $this->ILOs,
            'level' => $this->level,
            'total_credits' => $this->total_credits
        ];

        $file = [
            'name' => 'metadata.json',
            'content' => json_encode($content, JSON_PRETTY_PRINT)
        ];
        return $file;
    }
}
