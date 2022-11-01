<?php

/**
 * @package    local
 * @subpackage teal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     abhiandthetruth, thesmallstar
 */


require_once($CFG->dirroot . '/local/teal/helpers/LocalDatabase.php');
require_once($CFG->dirroot . '/local/teal/helpers/GlobalDatabase.php');
require_once($CFG->dirroot . '/local/teal/helpers/CourseHelper.php');
require_once($CFG->dirroot . '/local/teal/vendor/autoload.php');

class Course
{

    /****** Attributes ******/

    // ID used in the local teal metadata table
    // Initialized only after first local commit
    public $id;

    // Moodle Course intrinsic ID, links metadata table to moodle course table 
    // Inititalized only after moodle course creation
    public $moodle_course_id;

    // Commit id associated with the version fetched for the repo of this course
    // Initialized only after first github commit or course import
    public $commit;

    // All the fields of the course
    // Initialized every time object instantiates
    public $name;
    public $code;
    public $objective;
    public $level;
    public $creation_time;
    public $last_modified_time;
    public $course_details;
    public $course_structure;
    public $moderator;
    public $prerequisites;
    public $domain;
    public $sub_domain;
    public $skill;
    public $ILOs;
    public $total_credits; // computed field


    /****** Constructors ******/

    public function __construct(
        string $name,
        string $objective,
        string $level,
        string $creation_time,
        string $last_modified_time,
        array $course_details,
        array $course_structure,
        array $moderator,
        array $prerequisites,
        string $domain,
        string $sub_domain,
        string $skill,
        array $ILOs,
        string $code,
        string $id = null,
        string $moodle_course_id = null,
        string $commit = null
    ) {
        $this->name = $name;
        $this->objective = $objective;
        $this->level = $level;
        $this->creation_time = $creation_time;
        $this->last_modified_time = $last_modified_time;
        $this->course_details = $course_details;
        $this->course_structure = $course_structure;
        $this->moderator = $moderator;
        $this->prerequisites = $prerequisites;
        $this->domain = $domain;
        $this->sub_domain = $sub_domain;
        $this->skill = $skill;
        $this->ILOs = $ILOs;
        $this->code = $code;
        $this->id = $id;
        $this->moodle_course_id = $moodle_course_id;
        $this->commit = $commit;
        $this->total_credits = CourseHelper::calculate_total_credits_from_ILOs($ILOs);
    }

    public static function by_form_data($form_data)
    {
        // first create course_details , course_structure and moderator array
        $course_structure_arr = array(($form_data->lecture), $form_data->tutorial, $form_data->practical);
        
        $c_level = array('Beginner', 'Standard', 'Advanced');
        $c_units = array('Theory', 'Practical', 'Theory and Practical');
        $c_type = array('Core', 'Elective');
        $course_details_arr = array($c_type[$form_data->c_type], $c_units[$form_data->c_unit], $c_level[$form_data->c_level]);
        
        $moderator_arr = array($form_data->moderator_name, $form_data->moderator_email);

        $instance = new self(
            $form_data->name,
            $form_data->objective,
            $form_data->level,
            $form_data->creation_time,
            $form_data->last_modified_time,
            $course_details_arr,
            $course_structure_arr,
            $moderator_arr,
            $form_data->prerequisites,
            $form_data->domain,
            $form_data->sub_domain,
            $form_data->skill,
            $form_data->ILOs,
            CourseHelper::generate_course_code_from_course_name($form_data->name)
        );
        return $instance;
    }

    public static function by_repo_details($repo_name, $commit)
    {
        $course_details = (object)GlobalDatabase::get_file_from_commit(
            $repo_name,
            "metadata.json",
            $commit
        );
        $instance = new self(
            $course_details->name,
            $course_details->objective,
            $course_details->level,
            $course_details->creation_time,
            $course_details->last_modified_time,
            $course_details->course_details,
            $course_details->course_structure,
            $course_details->moderator,
            $course_details->prerequisites,
            $course_details->domain,
            $course_details->sub_domain,
            $course_details->skill,
            $course_details->ILOs,
            $course_details->code,
            null, // local db id
            null, // local moodle course id
            $commit
        );
        return $instance;
    }

    public static function by_form_data_with_id($form_data)
    {
        $course_temp = Course::by_id($form_data->id);

        $instance = new self(
            $course_temp->name,
            $form_data->objective,
            $course_temp->level,
            $form_data->creation_time,
            $form_data->last_modified_time,
            $form_data->course_details,
            $form_data->course_structure,
            $form_data->moderator,
            $form_data->prerequisites,
            $form_data->domain,
            $form_data->sub_domain,
            $form_data->skill,
            $form_data->ILOs,
            $course_temp->code,
            $form_data->id,
            $course_temp->moodle_course_id
        );

        return $instance;
    }

    public static function by_code(string $code)
    {
        $course_record = LocalDatabase::get_course_record_by_code($code);
        $instance = new self(
            $course_record->name,
            $course_record->objective,
            $course_record->level,
            $course_record->creation_time,
            $course_record->last_modified_time,
            json_decode($course_record->course_details),
            json_decode($course_record->course_structure),
            json_decode($course_record->moderator),
            json_decode($course_record->prerequisites),
            $course_record->domain,
            $course_record->sub_domain,
            $course_record->skill,
            json_decode($course_record->ILOs),
            $code,
            $course_record->id,
            isset($course_record->moodle_course_id) ? $course_record->moodle_course_id : null,
            isset($course_record->commit) ? $course_record->commit : null
        );

        return $instance;
    }

    public static function by_id(string $id)
    {
        $course_record = LocalDatabase::get_course_record_by_id($id);
        $instance = new self(
            $course_record->name,
            $course_record->objective,
            $course_record->level,
            $course_record->creation_time,
            $course_record->last_modified_time,
            json_decode($course_record->course_details),
            json_decode($course_record->course_structure),
            json_decode($course_record->moderator),
            json_decode($course_record->prerequisites),
            $course_record->domain,
            $course_record->sub_domain,
            $course_record->skill,
            json_decode($course_record->ilos),
            $course_record->code,
            $course_record->id,
            isset($course_record->moodle_course_id) ? $course_record->moodle_course_id : null,
            isset($course_record->commit) ? $course_record->commit : null
        );

        return $instance;
    }

    /****** Logic Functions ******/

    public function commit_to_local()
    {
        $course_record = (object)(array)$this;
        $course_record->ilos = json_encode($this->ILOs);
        $course_record->course_details = json_encode($this->course_details);
        $course_record->course_structure = json_encode($this->course_structure);
        $course_record->moderator = json_encode($this->moderator);
        $course_record->prerequisites = json_encode($this->prerequisites);
        if (!isset($this->id)) $this->id = LocalDatabase::insert_course_record($course_record);
        else LocalDatabase::update_course_record($course_record);
    }

    public function commit_to_global($commit_message)
    {
        $file_info = GlobalDatabase::commit_file_on_repo(
            $this->get_course_metadata_file_as_array(),
            $this->get_repo_name(),
            $commit_message
        );
        $this->commit = $file_info['commit']['sha'];
        $this->commit_to_local();
    }

    public function create_moodle_course()
    {
        if (isset($this->moodle_course_id)) return;
        $moodle_course_data = new stdClass();
        $moodle_course_data->shortname = $this->code;
        $moodle_course_data->fullname = $this->name;
        $moodle_course_data->category = "1"; // miscellaneous
        $moodle_course = create_course($moodle_course_data);
        $this->moodle_course_id = $moodle_course->id;
    }

    public function get_github_url()
    {
        return "https://github.com/" . LocalDatabase::getTealSettings()['org_global'] . "/" . $this->get_repo_name();
    }



    public function get_repo_name()
    {
        return str_replace(' ', '_', $this->code . '_' . $this->name);
    }


    /** Private Utilities **/

    private function get_course_metadata_file_as_array()
    {
        $content = [
            'name' => $this->name,
            'code' => $this->code,
            'objective' => $this->objective,
            'creation_time' => $this->creation_time,
            'last_modified_time' => $this->last_modified_time,
            'course_details' => $this->course_details,
            'course_structure' => $this->course_structure,
            'moderator' => $this->moderator,
            'prerequisites' => $this->prerequisites,
            'domain' => $this->domain,
            'sub_domain' => $this->sub_domain,
            'skill' => $this->skill,
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
