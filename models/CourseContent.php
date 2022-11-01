<?php

/**
 * @package    local
 * @subpackage teal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     abhiandthetruth, thesmallstar
 */

require_once($CFG->dirroot . '/config.php');
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
require_once($CFG->dirroot . '/backup/converter/convertlib.php');
require_once($CFG->dirroot . '/local/teal/helpers/LocalDatabase.php');
require_once($CFG->dirroot . '/local/teal/helpers/GlobalDatabase.php');
require_once($CFG->dirroot . '/local/teal/vendor/autoload.php');

class CourseContent
{
    // ID used in the local Course Content metadata table
    // Initialized only after first local commit
    public $id;

    // All the fields of the course
    // Initialized every time object instantiates
    public $name;
    public $code;
    public $description;

    public function __construct(
        string $name,
        string $description,
        $code = null
    ) {
        $this->name = $name;
        $this->description = $description;
        if ($code == null) {
            $this->code = $this->generate_code_from_course_name($name);
        } else {
            $this->code = $code;
        }
    }

    public static function generate_code_from_course_name($content_name)
    {
        $random_number = mt_rand(100, 999);
        $name_without_space = str_replace(' ', '', $content_name);
        return "CCN" . strval($random_number) . substr($name_without_space, 0, 2) . '_';
    }

    public function get_github_repo_name()
    {
        return $this->code . str_replace(' ', '_', $this->name);
    }

    public function create_course_content_from_local_course($form_data)
    {
        GlobalDatabase::commit_file_on_repo(
            $this->get_backup_file_from_courseID(
                $form_data->course_id
            ),
            $this->get_github_repo_name(),
            $form_data->commit_message
        );
    }

    public static function by_form_data($form_data)
    {
        $instance = new self(
            $form_data->name,
            $form_data->description
        );
        return $instance;
    }

    private function get_backup_file_from_courseID($id)
    {
        global $USER;
        $course_module_to_backup = $id; // Set this to one existing choice cmid in your dev site
        $user_doing_the_backup   = $USER->id; // Set this to the id of your admin account

        $bc = new backup_controller(
            backup::TYPE_1COURSE,
            $course_module_to_backup,
            backup::FORMAT_MOODLE,
            backup::INTERACTIVE_YES,
            backup::MODE_GENERAL,
            $user_doing_the_backup
        );

        // Set the default filename.
        $format = $bc->get_format();
        $type = $bc->get_type();
        $id = $bc->get_id();
        $users = $bc->get_plan()->get_setting('users')->get_value();
        $anonymised = $bc->get_plan()->get_setting('anonymize')->get_value();
        $filename = backup_plan_dbops::get_default_backup_filename($format, $type, $id, $users, $anonymised);
        $bc->get_plan()->get_setting('filename')->set_value($filename);

        // Execution.
        $bc->finish_ui();
        $bc->execute_plan();
        $results = $bc->get_results();
        $file = $results['backup_destination'];
        $upload_file = [
            "name" => "backup.mdz",
            "content" => $file->get_content()
        ];
        return $upload_file;
    }
}
