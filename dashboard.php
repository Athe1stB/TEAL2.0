<?php

/**
 * @package    local
 * @subpackage teal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     abhiandthetruth, thesmallstar
 */
require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/local/teal/forms/TealSettingsForm.php');
require_once($CFG->dirroot . '/local/teal/helpers/LocalDatabase.php');
require_login();

// Settings up the page
$PAGE->set_pagelayout('standard');
$PAGE->set_url(new moodle_url('/local/teal/dashboard.php'));
$PAGE->set_title('Teal Dashboard');
$PAGE->set_heading('Dashboard');

// Initializing the form
$settings_form = new TealSettingsForm();

// Manging form responses
if ($settings_form->is_cancelled())
    redirect($CFG->wwwroot . '/local/teal/dashboard.php', 'Oops settings updation cancelled!');
else if ($form_response = $settings_form->get_data()) {
    LocalDatabase::update_teal_settings_from_form($form_response, true);
    redirect($CFG->wwwroot . '/local/teal/dashboard.php', 'Setting has been updated!');
}

// Template Data
$course_create_url = new moodle_url("/local/teal/course/create.php");
$course_content_create_url = new moodle_url("/local/teal/course_content/create.php");
$program_create_url = new moodle_url("/local/teal/program/create.php");
$courses_url = new moodle_url("/local/teal/course/list.php");
$programs_url = new moodle_url("/local/teal/program/list.php");
$template_data = (object)[
    "course_create_url" => $course_create_url->__toString(),
    "course_content_create_url" => $course_content_create_url->__toString(),
    "program_create_url" => $program_create_url->__toString(),
    "courses_url" => $courses_url->__toString(),
    "programs_url" => $programs_url->__toString()
];

//HTML
echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_teal/dashboard', $template_data);
$settings_form->display();
echo $OUTPUT->footer();
