<?php

/**
 * @package    local
 * @subpackage teal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     abhiandthetruth, thesmallstar
 */

require_once($CFG->dirroot . '/local/teal/helpers/LocalDatabase.php');
require_once($CFG->dirroot . '/local/teal/vendor/autoload.php');

class GlobalDatabase
{
    private static $github_client;

    /*
        Settings contain the following
        - github_access_token   : token of the user account connected with current teal instance
        - org_global            : name of the organisation under which data is organised
    */


    public static function get_github_client()
    {
        if (!isset(self::$github_client)) {
            self::$github_client = new \Github\Client();
            $settings = LocalDatabase::getTealSettings();
            self::$github_client->authenticate($settings['github_access_token'], null, Github\Client::AUTH_ACCESS_TOKEN);
        }
        return self::$github_client;
    }

    public static function get_repo_names()
    {
        $global_org = LocalDatabase::getTealSettings()['org_global'];
        $repos = self::get_github_client()->api('repo')->org($global_org, ['per_page' => 100]);

        $repo_names = array();
        foreach ($repos as $repo) {
            array_push($repo_names, $repo['name']);
        }

        return $repo_names;
    }

    public static function get_repo_branch_names($repo_name)
    {
        $global_org = LocalDatabase::getTealSettings()['org_global'];
        $branches = self::get_github_client()->client->api('gitData')->references()->branches($global_org, $repo_name);
        return $branches;
    }

    public static function create_repo($repo_name)
    {
        $global_org = LocalDatabase::getTealSettings()['org_global'];
        $repo_visibility = false;
        $repo_details = "";

        $repo = self::get_github_client()->api('repo')->create(
            $repo_name,
            $repo_details,
            '',
            $repo_visibility,
            $global_org,
            false,
            false,
            false,
            null,
            true,
            true
        );

        return $repo;
    }

    public static function create_repo_branch($repo_name, $branch_name)
    {
        $global_org = LocalDatabase::getTealSettings()['org_global'];
        // Get the latest commits
        $commits = self::get_github_client()->api('repo')->commits()->all(
            $global_org,
            $repo_name,
            array('sha' => 'main')
        );

        // Decide on the initial commit and create a new branch 
        $initialCommitSha = $commits[0]['sha'];
        $referenceData = ['ref' => "refs/heads/${branch_name}", 'sha' => $initialCommitSha];
        $reference = self::get_github_client()->api('gitData')->references()->create(
            $global_org,
            $repo_name,
            $referenceData
        );
    }

    public static function create_file_on_repo_branch($file, $repo_name, $commit_message)
    {
        $global_org = LocalDatabase::getTealSettings()['org_global'];
        $branch_name = self::get_branch_name();
        $file_info = self::get_github_client()->api('repo')->contents()->create(
            $global_org,
            $repo_name,
            $file['name'],
            $file['content'],
            $commit_message,
            $branch_name,
            null // committer
        );
        return $file_info;
    }

    public static function update_file_on_repo_branch($file, $repo_name, $commit_message, $parent_commit = null)
    {
        $global_org = LocalDatabase::getTealSettings()['org_global'];
        $branch_name = self::get_branch_name();
        $oldFile = self::get_github_client()->api('repo')->contents()->show($global_org, $repo_name, $file["name"], $branch_name);

        $file_info = self::get_github_client()->api('repo')->contents()->update(
            $global_org,
            $repo_name,
            $file["name"],
            $file["content"],
            $commit_message,
            $oldFile['sha'],
            $branch_name,
            null // committer
        );

        return $file_info;
    }

    public static function commit_file_on_repo($file, $repo_name, $commit_message)
    {
        $global_org = LocalDatabase::getTealSettings()['org_global'];
        $branch_name = self::get_branch_name();
        $create_new_file = false;

        try {
            self::get_github_client()->api('repo')->show($global_org, $repo_name);
        } catch (Exception $e) {
            self::create_repo($repo_name);
            $create_new_file = true;
        }

        try {
            self::get_github_client()->api('repo')->branches($global_org, $repo_name, $branch_name);
        } catch (Exception $e) {
            self::create_repo_branch($repo_name, $branch_name);
            $create_new_file = true;
        }

        $file_info = null;
        if ($create_new_file) {
            $file_info = self::create_file_on_repo_branch($file, $repo_name, $commit_message);
        } else {
            $file_info = self::update_file_on_repo_branch($file, $repo_name, $commit_message);
        }
        return $file_info;
    }

    public static function get_latest_commit_for_repo($repo_name)
    {
        $global_org = LocalDatabase::getTealSettings()['org_global'];
        $branches = self::get_github_client()->api('repo')->branches(
            $global_org,
            $repo_name
        );
        $commit_sha = "";
        foreach ($branches as $branch) {
            if ($branch['name'] != "main") {
                $commit_sha = $branch["commit"]["sha"];
            }
        }
        return $commit_sha;
    }

    public static function get_file_from_commit($repo_name, $file_name, $commit = null)
    {
        if (!$commit) $commit = self::get_latest_commit_for_repo($repo_name);
        $global_org = LocalDatabase::getTealSettings()['org_global'];
        $raw_content = self::get_github_client()->api('repo')->contents()->show(
            $global_org,
            $repo_name,
            $file_name,
            $commit
        )["content"];
        $decoded_content = json_decode(base64_decode($raw_content), true);
        return $decoded_content;
    }

    public static function get_branches_for_repo($repo_name)
    {
        $global_org = LocalDatabase::getTealSettings()['org_global'];
        $branches = self::get_github_client()->api('gitData')->references()->branches($global_org, $repo_name);
        return $branches;
    }

    public static function get_commits_for_branch($repo_name, $branch_name)
    {
        $global_org = LocalDatabase::getTealSettings()['org_global'];
        $commits = self::get_github_client()->api('repo')->commits()->all($global_org, $repo_name, array('sha' => $branch_name));
        return $commits;
    }

    private static function get_branch_name()
    {
        return self::get_github_client()->api('me')->show()["login"];
    }
}
