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
$PAGE->set_url(new moodle_url('/local/teal/course/create.php'));
$PAGE->set_title('Create Course');
$PAGE->set_heading('Create Course');
$PAGE->requires->js('/local/teal/js/course/create.js');

echo $OUTPUT->header();

$create_course_form = new CreateCourseForm();

function debug_to_console($data) {
    $output = $data;
    if (is_array($output))
    {
        $output = implode(',', $output);
    }

    echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
}

if ($create_course_form->is_cancelled()) {
    redirect(
        $CFG->wwwroot . '/local/teal/dashboard.php',
        'Oops course creation cancelled!'
    );
} else if ($form_data = $create_course_form->get_data()) {
    // set creation date here
    date_default_timezone_set("Asia/Calcutta");
    $date_string = date("h:i:s A, d-M-Y");
    $form_data->creation_time = $date_string;
    $form_data->last_modified_time = $date_string;

    // convert coures structure text to integer
    $form_data->lecture = (int)$form_data->lecture;
    $form_data->tutorial = (int)$form_data->tutorial;
    $form_data->practical = (int)$form_data->practical;

    if ($form_data->branch_hid == "")
        $course = Course::by_form_data($form_data);
    else
        $course = Course::by_repo_details(
            $form_data->repo_name,
            $form_data->commit_hid
        );

    debug_to_console($date_string);
    $course->create_moodle_course();
    $course->commit_to_local();
    $course->commit_to_global("First commit");
    redirect(
        $CFG->wwwroot . '/local/teal/dashboard.php',
        'Course has been created!'
    );
}

$create_course_form->display();
echo $OUTPUT->footer();
