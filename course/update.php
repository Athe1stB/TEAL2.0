<?php

/**
 * @package    local
 * @subpackage teal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     abhiandthetruth, thesmallstar
 */
require_once(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot . '/local/teal/forms/UpdateCourseForm.php');
require_once($CFG->dirroot . '/local/teal/models/Course.php');
require_login();


$PAGE->set_pagelayout('standard');
$update_url = new moodle_url('/local/teal/course/update.php?id=' . $_GET['id']);
$PAGE->set_url(new moodle_url('/local/teal/course/update.php'));
$PAGE->set_title('Update Course');
$PAGE->set_heading('Update Course');
$PAGE->requires->js('/local/teal/js/course/create.js');


$update_course_form = new UpdateCourseForm($_GET['id'], $update_url);

echo $OUTPUT->header();

if ($update_course_form->is_cancelled()) {
    redirect($CFG->wwwroot . '/local/teal/dashboard.php', 'Oops course update cancelled!');
} else if ($form_data = $update_course_form->get_data()) {
    $form_data->id = $_GET['id'];
    $course =  Course::by_form_data_with_id($form_data);
    $course->commit_to_local();
    $course->commit_to_global($form_data->commit_message);
    redirect($CFG->wwwroot . '/local/teal/dashboard.php', 'Course has been updated');
}
$update_course_form->display();
echo $OUTPUT->footer();
