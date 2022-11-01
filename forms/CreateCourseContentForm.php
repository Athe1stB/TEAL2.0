<?php

/**
 * @package    local
 * @subpackage teal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     abhiandthetruth, thesmallstar
 */


require_once("$CFG->libdir/formslib.php");
require_once($CFG->dirroot . '/local/teal/helpers/LocalDatabase.php');
require_once($CFG->dirroot . '/local/teal/helpers/GlobalDatabase.php');
require_once($CFG->dirroot . '/local/teal/helpers/CAH3Classifier.php');
require_once($CFG->dirroot . '/local/teal/helpers/ILOTaxonomy.php');
require_once($CFG->dirroot . '/local/teal/helpers/CourseHelper.php');

class CreateCourseContentForm extends moodleform
{

    //Add elements to form
    public function definition()
    {
        // Initialize Form
        $form = $this->_form;

        // Course Name
        $course_repo_names = CourseHelper::get_local_repo_names();
        $form->addElement('select', 'course_id', 'Course', $course_repo_names);
        $form->addElement('text', 'name', 'Name');
        $form->addElement('text', 'description', 'Description');
        $form->addElement('text', 'commit_message', 'Commit Message');

        // Adding action button
        $this->add_action_buttons(true, "Create");
    }
}
