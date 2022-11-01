<?php

/**
 * @package    local
 * @subpackage teal
 * 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     abhiandthetruth, thesmallstar
 */
require_once(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot . '/local/teal/helpers/LocalDatabase.php');
require_once($CFG->dirroot . '/local/teal/models/Program.php');
require_login();

$PAGE->set_pagelayout('standard');
$PAGE->set_url(new moodle_url('/local/teal/course/view.php'));
$PAGE->set_title('Program');

if (isset($_GET["id"])) $program = Program::by_id($_GET["id"]);
else $program = Program::by_repo_name($_GET["repoName"]);
$PAGE->set_heading($program->name);
$github_url = $program->get_github_url();

// Process learning outcome array into a list
$learning_outcomes = $program->ILOs;
$learningOutcomeString = '';
$i = 1;
foreach ($learning_outcomes as $outcomes) {
    foreach ($outcomes as $outcome) {
        $outcome = (object)$outcome;
        $learningOutcomeString .= "{$i}) " . $outcome->statement . "<br />";
        $i++;
    }
}

// Process course array into list
$courses = $program->courses;
$courseString = '';
$i = 1;
foreach ($courses as $course) {
    $course = (object) $course;
    $courseString .= "{$i}) " . $course->repo_name . "<br />";
    $i++;
}

$update_url = new moodle_url("/local/teal/course/update.php");
$data = (object)[
    "program" => $program,
    "github_url" => $github_url,
    "learning_outcomes" => $learningOutcomeString,
    "courses" => $courseString,
    "is_global" => !isset($_GET["id"]),
    "import_url" => 'import.php?repoName=' . $program->get_repo_name()
];

//HTML
echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_teal/program_view', $data);
echo $OUTPUT->footer();
