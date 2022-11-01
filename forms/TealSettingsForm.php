<?php

/**
 * @package    local
 * @subpackage teal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     abhiandthetruth, thesmallstar
 */

require_once("$CFG->libdir/formslib.php");

class TealSettingsForm extends moodleform
{
   //Add elements to form
   public function definition()
   {
       global $DB;

       // Get the form from class
       $form = $this->_form;

       // Get the records for settings
       $settings = $DB->get_records('teal_settings');

       // Add the text fields
       foreach ($settings as $setting) {
           $form->addElement(
               $setting->is_secret? 'password':'text',
               $setting->id,
               get_string($setting->name, 'local_teal'),
               array('autocomplete' => 'false')
           );
           if (!$setting->is_secret)
               $form->setDefault($setting->id, $setting->value);
       }

       // Add Submit and Cancel button
       $this->add_action_buttons();
   }
}
