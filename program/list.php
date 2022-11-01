<?php

/**
 * @package    local
 * @subpackage teal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     abhiandthetruth, thesmallstar
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot . '/local/teal/models/Program.php');
require_once($CFG->dirroot . '/local/teal/helpers/ProgramHelper.php');

require_login();

$PAGE->set_pagelayout('standard');
$PAGE->set_url(new moodle_url('/local/teal/program/list.php'));
$PAGE->set_title('Program List');
$PAGE->set_heading('Program List');

// Getting all the locally present courses
$synced_programs = LocalDatabase::get_program_records();
$synced_program_repo_names = [];
foreach ($synced_programs as $synced_program)
    array_push(
        $synced_program_repo_names,
        ProgramHelper::get_program_repo_name_from_program_record($synced_program)
    );

// Getting all the globally present courses not in local
$all_program_repo_names = [];
try {
    $all_program_repo_names = ProgramHelper::get_program_repo_names();
} catch (\Throwable $th) {
}

$non_synced_programs = [];
foreach ($all_program_repo_names as $program_repo_name) {
    if (in_array($program_repo_name, $synced_program_repo_names, true)) continue;
    $program = new stdClass();
    $program->repo_name = $program_repo_name;
    $program->name = ProgramHelper::get_program_name_from_program_repo_name($program_repo_name);
    $program->code = ProgramHelper::get_program_code_from_program_repo_name($program_repo_name);
    array_push($non_synced_programs, $program);
}

//HTML
$program_url = new moodle_url("/local/teal/program/view.php");
echo $OUTPUT->header();
$data = (object)[
    "programs" => array_values($synced_programs),
    "non_synced_programs" => array_values($non_synced_programs),
    "program_url" => $program_url->__toString()
];
echo $OUTPUT->render_from_template('local_teal/program_list', $data);
echo $OUTPUT->footer();
