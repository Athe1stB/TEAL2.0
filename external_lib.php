<?php

/**
 * @package    local
 * @subpackage teal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     abhiandthetruth, thesmallstar
 */


require_once($CFG->libdir . "/externallib.php");
require_once($CFG->dirroot . '/local/teal/helpers/CAH3Classifier.php');
require_once($CFG->dirroot . '/local/teal/helpers/CourseHelper.php');
require_once($CFG->dirroot . '/local/teal/helpers/GlobalDatabase.php');

class ExternalCallHelper extends external_api
{

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_sub_domains_from_domain_parameters()
    {
        return new external_function_parameters(
            array("domain" => new external_value(PARAM_TEXT, "domain"))
        );
    }

    public static function get_sub_domains_from_domain($domain)
    {
        if ($domain == "") return "[]";
        return json_encode(CAH3Classifier::get_cah3_sub_domains_from_domain($domain));
    }

    public static function get_sub_domains_from_domain_returns()
    {
        return new external_value(PARAM_RAW, 'The updated JSON output');
    }

    public static function get_skills_from_sub_domain_parameters()
    {
        return new external_function_parameters(
            array(
                "domain" => new external_value(PARAM_TEXT, "domain"),
                "sub_domain" => new external_value(PARAM_TEXT, "subdomain")
            )
        );
    }

    public static function get_skills_from_sub_domain($domain, $sub_domain)
    {
        if ($domain == "" || $sub_domain == "") return "[]";
        return json_encode(CAH3Classifier::get_cah3_skills_from_sub_domain($domain, $sub_domain));
    }

    public static function get_skills_from_sub_domain_returns()
    {
        return new external_value(PARAM_RAW, 'The updated JSON output');
    }

    public static function get_course_details_from_repo_name_parameters()
    {
        return new external_function_parameters(
            array("repo_name" => new external_value(PARAM_TEXT, "repo_name"))
        );
    }

    public static function get_course_details_from_repo_name($repo_name)
    {
        if ($repo_name == "") return "[]";
        return json_encode(CAH3Classifier::get_cah3_sub_domains_from_domain($repo_name));
    }

    public static function get_course_details_from_repo_name_returns()
    {
        return new external_value(PARAM_RAW, 'The updated JSON output');
    }

    public static function get_course_repo_names_parameters()
    {
        return new external_function_parameters(
            array(
                "seed" => new external_value(PARAM_INT, "seed")
            )
        );
    }

    public static function get_course_repo_names($seed)
    {
        return json_encode(CourseHelper::get_course_repo_names());
    }

    public static function get_course_repo_names_returns()
    {
        return new external_value(PARAM_RAW, 'The updated JSON output');
    }

    public static function get_branches_for_course_repo_parameters()
    {
        return new external_function_parameters(
            array("course_repo" => new external_value(PARAM_TEXT, "course_repo"))
        );
    }

    public static function get_branches_for_course_repo($course_repo)
    {
        if ($course_repo == "") return "[]";
        return json_encode(GlobalDatabase::get_branches_for_repo($course_repo));
    }

    public static function get_branches_for_course_repo_returns()
    {
        return new external_value(PARAM_RAW, 'The updated JSON output');
    }

    public static function get_commits_for_branch_parameters()
    {
        return new external_function_parameters(
            array(
                "selected_course" => new external_value(PARAM_TEXT, "selected_course"),
                "selected_branch" => new external_value(PARAM_TEXT, "selected_branch")
            )
        );
    }

    public static function get_commits_for_branch($selected_course, $selected_branch)
    {
        if ($selected_branch == "") return "[]";
        return json_encode(GlobalDatabase::get_commits_for_branch($selected_course, $selected_branch));
    }

    public static function get_commits_for_branch_returns()
    {
        return new external_value(PARAM_RAW, 'The updated JSON output');
    }

    public static function get_course_metadata_from_commit_parameters()
    {
        return new external_function_parameters(
            array(
                "selected_course" => new external_value(PARAM_TEXT, "selected_course"),
                "selected_commit" => new external_value(PARAM_TEXT, "selected_commit")
            )
        );
    }

    public static function get_course_metadata_from_commit($selected_course, $selected_commit)
    {
        if ($selected_commit == "") return "[]";
        return json_encode(GlobalDatabase::get_file_from_commit($selected_course, "metadata.json", $selected_commit));
    }

    public static function get_course_metadata_from_commit_returns()
    {
        return new external_value(PARAM_RAW, 'The updated JSON output');
    }
}
