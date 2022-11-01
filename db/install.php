<?php

function xmldb_local_teal_install()
{
    $initial_settings = [
        (object)[
            "name" => "github_access_token",
            "value" => "",
            "is_secret" => 1
        ],
        (object)[
            "name" => "org_global",
            "value" => "",
            "is_secret" => 0
        ]
    ];

    global $DB;

    $DB->insert_records('teal_settings', $initial_settings);
}
