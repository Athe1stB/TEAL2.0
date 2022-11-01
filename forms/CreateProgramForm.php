<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * @package     local_teal
 * @author      abhiandthetruth, thesmallstar
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * 
 * 
 */
require_once("$CFG->libdir/formslib.php");
require_once($CFG->dirroot . '/local/teal/vendor/autoload.php');
require_once($CFG->dirroot . '/local/teal/helpers/LocalDatabase.php');
require_once($CFG->dirroot . '/local/teal/helpers/CourseHelper.php');

class CreateProgramForm extends moodleform
{
    //Add elements to form
    public function definition()
    {
        global $DB;
        $mform = $this->_form;

        $local_courses = LocalDatabase::get_course_records();

        // Program Name
        $mform->addElement('text', 'name', 'Program name');

        // Program Objective
        $mform->addElement('text', 'objective', 'Program objective');


        // Course Level
        $options2 = array(
            'UG' => 'UG',
            'PG' => 'PG'
        );

        $mform->addElement('select', 'level', 'Program Level', $options2);
        $mform->setDefault('level', ['UG']);

        $options = [];
        foreach ($local_courses as $course) {
            $options[$course->id] = $course->name . "(" . $course->code . ")";
        }

        $options2 = array(
            'multiple' => true,
            'noselectionstring' => "Select Courses",
        );

        $mform->addElement('autocomplete', 'course_ids', "Select Courses", $options, $options2);
        //   $mform->addElement('static', 'info', "Note that all courses not available globally will be exported online!");
        $mform->addElement('html', "<p>Note that program credits will be auto calculated from all courses constituting it.");
        // Add Submit and Cancel button
        $this->add_action_buttons($submitlabel = "Create");
    }

    //Custom validation should be added here
    function validation($data, $files)
    {
        return array();
    }
}
