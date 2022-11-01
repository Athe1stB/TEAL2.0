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


$program = Program::by_repo_name($_GET["repoName"]);
$program->commit_to_local();
redirect(
    $CFG->wwwroot . '/local/teal/dashboard.php',
    'Program has been imported!'
);
