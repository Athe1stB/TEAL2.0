<?php

/**
 * @package    local
 * @subpackage teal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     abhiandthetruth, thesmallstar
 */
require_once(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot . '/local/teal/vendor/autoload.php');
require_once($CFG->dirroot . '/local/teal/forms/CreateProgramForm.php');
require_once($CFG->dirroot . '/local/teal/models/Program.php');
require_once($CFG->dirroot . '/local/teal/helpers/LocalDatabase.php');
require_once($CFG->dirroot . '/local/teal/helpers/GlobalDatabase.php');
require_once($CFG->dirroot . '/local/teal/helpers/CAH3Classifier.php');
require_once($CFG->dirroot . '/local/teal/helpers/ILOTaxonomy.php');
require_once($CFG->dirroot . '/local/teal/helpers/CourseHelper.php');
require_login();
global $OUTPUT, $DB;

$PAGE->set_pagelayout('standard');
$PAGE->set_url(new moodle_url('/local/teal/program/create.php'));
$PAGE->set_title('Create Program');
$PAGE->set_heading('Create Program');

// Initialize Form
$createProgramForm = new CreateProgramForm();

// Handle Form Actions
if ($createProgramForm->is_cancelled()) {
    redirect($CFG->wwwroot . '/local/teal/dashboard/home.php', 'Oops program create cancelled!');
} else if ($form_data = $createProgramForm->get_data()) {
    $program = Program::by_form_data($form_data);
    $program->commit_to_local();
    $program->commit_to_global("First Commit");
    redirect($CFG->wwwroot . '/local/teal/dashboard.php', 'Program has been created!');
}

// Render everything
echo $OUTPUT->header();
$createProgramForm->display();
echo $OUTPUT->footer();
