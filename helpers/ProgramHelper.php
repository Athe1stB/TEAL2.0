<?php

/**
 * @package    local
 * @subpackage teal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     abhiandthetruth, thesmallstar
 */

require_once($CFG->dirroot . '/local/teal/helpers/GlobalDatabase.php');

class ProgramHelper
{
    public const PROGRAM_CODE_LENGTH = 8;
    public const PROGRAM_CODE_PREFIX = "PGM";
    public const PROGRAM_LEVELS = ["UG", "PG"];
    public const PROGRAM_FILE_NAME = "metadata.json";

    public static function get_program_name_from_program_repo_name(string $program_repo_name)
    {
        return str_replace("_", " ", substr($program_repo_name, self::PROGRAM_CODE_LENGTH));
    }

    public static function get_program_code_from_program_repo_name(string $program_repo_name)
    {
        return substr($program_repo_name, 0, self::PROGRAM_CODE_LENGTH);
    }

    public static function get_program_repo_name_from_program_record($program_record)
    {
        return str_replace(' ', '_', $program_record->code . '_' . $program_record->name);
    }

    public static function get_program_repo_names()
    {
        $repo_names = GlobalDatabase::get_repo_names();
        $program_repo_names = [];
        foreach ($repo_names as $repo_name) {
            if (substr($repo_name, 0, strlen(self::PROGRAM_CODE_PREFIX)) == self::PROGRAM_CODE_PREFIX)
                array_push($program_repo_names, ($repo_name));
        }
        return $program_repo_names;
    }

    public static function get_program_details_from_repo_name($repo_name)
    {
        return GlobalDatabase::get_file_from_commit($repo_name, self::PROGRAM_FILE_NAME, null);
    }
}
