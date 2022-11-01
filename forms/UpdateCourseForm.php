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

class UpdateCourseForm extends moodleform
{
    public $course_id;

    function __construct($course_id, $update_url)
    {
        $this->course_id = $course_id;
        parent::__construct($update_url->__toString());
    }

    //Add elements to form
    public function definition()
    {
        // Initialize Form
        $form = $this->_form;

        $course = Course::by_id($this->course_id);
        $course->name;
        $form->addElement('static', 'name', "Course Name", $course->name);

        // Course Objective
        $objective = $form->addElement('text', 'objective', 'Objective');
        $objective->setValue($course->objective);

        // Course level
        $course_levels = CourseHelper::get_course_levels_list();
        $course_levels_map = [];
        foreach ($course_levels as $course_level)
            $course_levels_map[$course_level] = $course_level;

        $form->addElement('static', 'level', "Level", $course->level);


        /****  Course CAH3 Classification *****/


        // Course CAH3 Classification Label
        $form->addElement('static', 'cah3_classification_label', "<b>CAH3 Classification</b>", "<hr />");

        // Course Domain
        $course_domains = CAH3Classifier::get_cah3_domains();
        $course_domains_map = ["" => "Select Domain"];
        foreach ($course_domains as $course_domain)
            $course_domains_map[$course_domain] = $course_domain;
        $course_domain_field = $form->addElement('select', 'domain', 'Domain', $course_domains_map);
        $form->setDefault("domain", $course->domain);
        // Course Sub Domain
        $course_sub_domains = CAH3Classifier::get_cah3_sub_domains();
        $course_sub_domains_map = ["" => "Select Sub Domain"];
        foreach ($course_sub_domains as $course_sub_domain)
            $course_sub_domains_map[$course_sub_domain] = $course_sub_domain;
        $form->addElement('select', 'sub_domain', 'Sub Domain', $course_sub_domains_map);
        $form->setDefault("sub_domain", $course->sub_domain);
        // Course Skill
        $course_skills = CAH3Classifier::get_cah3_skills();
        $course_skills_map = ["" => "Select Skills"];
        foreach ($course_skills as $course_skill) {
            $course_skills_map[$course_skill] = $course_skill;
        }
        $form->addElement('select', 'skill', 'Skills', $course_skills_map);
        $form->setDefault("skill", $course->skill);

        /****  Course ILO Specification *****/


        // Course ILO label
        $form->addElement('static', 'ILO_specification_label', "<b>ILO Specification</b>", "<hr />");

        // Course ILO Level
        $ILO_levels = ILOTaxonomy::get_ILO_levels();
        $ILO_levels_map = ["" => "Select SOLO/BLOOMS level"];
        foreach ($ILO_levels as $ILO_level)
            $ILO_levels_map[$ILO_level] = $ILO_level;
        $ILO_level_input = $form->createElement('select', 'level', 'Level', $ILO_levels_map);

        // Course ILO Verb
        $ILO_verbs = ILOTaxonomy::get_ILO_verbs();
        $ILO_verbs_map = ["" => "Select Verb"];
        foreach ($ILO_verbs as $ILO_verb)
            $ILO_verbs_map[$ILO_verb] = $ILO_verb;
        $ILO_verb_input = $form->createElement('select', 'verb', 'Verb', $ILO_verbs_map);

        // Course ILO statement
        $ILO_statement_input = $form->createElement('text', 'statement', 'Statement', ["placeholder" => "Write ILO Statement"]);

        // Course ILO Credit
        $ILO_credit_input = $form->createElement('text', 'credit', 'Credit', ["placeholder" => "Credits"]);

        //Course ILO Delete Button
        $ILO_delete_button = $form->createElement('submit', 'ILO_delete_button', "Delete Outcome");

        // ILO Group
        $ILO_group = $form->createElement('group', 'ILOs', "Outcome {no} Description", [$ILO_level_input, $ILO_verb_input, $ILO_statement_input, $ILO_credit_input]);
        $this->repeat_elements(
            [$ILO_group, $ILO_delete_button],
            1,
            [],
            'ILO_repeats',
            'ILO_add_fields',
            1,
            "Add {no} more outcome(s)",
            true,
            "ILO_delete_button"
        );

        // Message
        $form->addElement('text', 'commit_message', 'Message');

        // Adding action button
        $this->add_action_buttons(true, "Update");
    }
}
