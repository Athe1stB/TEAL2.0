<?php

/**
 * @package    local
 * @subpackage teal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     abhiandthetruth, thesmallstar
 */
require_once(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot . '/local/teal/forms/CreateCourseForm.php');
require_once($CFG->dirroot . '/local/teal/models/Course.php');
require_login();

$PAGE->set_pagelayout('standard');
$PAGE->set_url(new moodle_url('/local/teal/course/list.php'));
$PAGE->set_title('Course List');
$PAGE->set_heading('Course List');

// Getting all the locally present courses
$synced_courses = LocalDatabase::get_course_records();

//HTML
$course_url = new moodle_url("/local/teal/course/view.php");
echo $OUTPUT->header();
$data = (object)[
    "courses" => array_values($synced_courses),
    "course_url" => $course_url->__toString()
];
echo $OUTPUT->render_from_template('local_teal/course_list', $data);
echo $OUTPUT->footer();
