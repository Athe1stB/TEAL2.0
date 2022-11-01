<?php

/**
 * @package    local
 * @subpackage teal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     abhiandthetruth, thesmallstar
 */
require_once(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot . '/local/teal/forms/CreateCourseContentForm.php');
require_once($CFG->dirroot . '/local/teal/models/CourseContent.php');
require_login();

$PAGE->set_pagelayout('standard');
$PAGE->set_url(new moodle_url('/local/teal/course_content/create.php'));
$PAGE->set_title('Create Course Content');
$PAGE->set_heading('Create Course Content');
$PAGE->requires->js('/local/teal/js/course/create.js');

$create_course_form = new CreateCourseContentForm();

echo $OUTPUT->header();

$create_course_form->display();

if ($create_course_form->is_cancelled()) {
    redirect($CFG->wwwroot . '/local/teal/dashboard.php', 'Oops course content creation cancelled!');
} else if ($form_data = $create_course_form->get_data()) {
    $form_data->course_id = intval($form_data->course_id);
    $course_content = CourseContent::by_form_data($form_data);
    $course_content->create_course_content_from_local_course($form_data);
    redirect($CFG->wwwroot . '/local/teal/dashboard.php', 'Course Content has been created!');
}

echo $OUTPUT->footer();
