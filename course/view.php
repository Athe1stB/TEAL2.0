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
$PAGE->set_url(new moodle_url('/local/teal/course/view.php'));
$PAGE->set_title('Course');

$course = Course::by_id($_GET["id"]);

$PAGE->set_heading($course->name);

$github_url = $course->get_github_url();

$learning_outcomes = $course->ILOs;
$learningOutcomeString = '';
$i = 1;
foreach ($learning_outcomes as $outcome) {
    $learningOutcomeString .= "{$i}) " . $outcome->statement . "<br />";
    $i++;
}

$prerequisites = $course->prerequisites;
$prerequisitesString = '';
$i = 1;
foreach ($prerequisites as $pre) {
    $prerequisitesString .= "{$i}) " . $pre . "<br />";
    $i++;
}

$details = $course->course_details;
$detailsStr = 'Type: ' . $details[0] . '<br/> Unit: ' . $details[1] . '<br/> Level: ' . $details[2];


$structure = $course->course_structure;
$structureStr = 'Lecture: ' . $structure[0] . '<br/> Tutorial: ' . $structure[1] . '<br/> Practical: ' . $structure[2];

$moderatorStr = $course->moderator[0] . '<br /> Email:  ' . $course->moderator[1];

$update_url = new moodle_url("/local/teal/course/update.php");
$data = (object)[
    "course" => $course,
    "github_url" => $github_url,
    "update_url" => $update_url->__toString(),
    'moderator' => $moderatorStr,
    'course_details' => $detailsStr,
    'course_structure' => $structureStr,
    "learning_outcomes" => $learningOutcomeString,
    "prerequisites" => $prerequisitesString,
];

//HTML
echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_teal/course_view', $data);
echo $OUTPUT->footer();