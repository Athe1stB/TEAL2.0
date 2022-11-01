<?php

function local_teal_extend_navigation(global_navigation $nav)
{
    $main_node = $nav->add("Manage Teal System", new moodle_url('/local/teal/dashboard.php'), navigation_node::TYPE_SETTING,  null, null, new pix_icon('i/cohort', ''));
    $main_node->nodetype = 1;
    $main_node->collapse = false;
    $main_node->force_open = true;
    $main_node->isexpandable = false;
    $main_node->showinflatnavigation = true;
}