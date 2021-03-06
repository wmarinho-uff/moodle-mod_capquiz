<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace mod_capquiz;

use mod_capquiz\capquiz;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * @package     mod_capquiz
 * @author      Aleksander Skrede <aleksander.l.skrede@ntnu.no>
 * @copyright   2018 NTNU
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class n_closest_configuration_form extends \moodleform {
    private $configuration;

    public function __construct(\stdClass $configuration, \moodle_url $url) {
        $this->configuration = $configuration;
        parent::__construct($url);

    }

    public function definition() {
        $form = $this->_form;

        $form->addElement('text', 'number_of_questions_to_select', get_string('number_of_questions_to_select', 'capquiz'));
        $form->setType('number_of_questions_to_select', PARAM_INT);
        $form->setDefault('number_of_questions_to_select', $this->configuration->number_of_questions_to_select);
        $form->addRule('number_of_questions_to_select', get_string('number_of_questions_to_select_required', 'capquiz'), 'required', null, 'client');
        $form->addHelpButton('number_of_questions_to_select', 'number_of_questions_to_select', 'capquiz');

        $form->addElement('text', 'user_win_probability', get_string('user_win_probability', 'capquiz'));
        $form->setType('user_win_probability', PARAM_FLOAT);
        $form->setDefault('user_win_probability', $this->configuration->user_win_probability);
        $form->addRule('user_win_probability', get_string('user_win_probability_required', 'capquiz'), 'required', null, 'client');
        $form->addHelpButton('user_win_probability', 'user_win_probability', 'capquiz');

        $form->addElement('text', 'prevent_same_question_for_turns', get_string('prevent_question_n_times', 'capquiz'));
        $form->setType('prevent_same_question_for_turns', PARAM_INT);
        $form->setDefault('prevent_same_question_for_turns', $this->configuration->prevent_same_question_for_turns);
        $form->addRule('prevent_same_question_for_turns', get_string('field_required', 'capquiz'), 'required', null, 'client');
        $form->addHelpButton('prevent_same_question_for_turns', 'prevent_question_n_times', 'capquiz');

        $this->add_action_buttons(false);
    }

    public function validations($data, $files) {
        $validation_errors = [];
        if (empty($data['user_win_probability'])) {
            $validation_errors['user_win_probability'] = get_string('user_win_probability_required', 'capquiz');
        }
        if (empty($data['number_of_questions'])) {
            $validation_errors['number_of_questions'] = get_string('number_of_questions_to_select_required', 'capquiz');
        }
        if (empty($data['prevent_same_question_for_turns'])) {
            $validation_errors['prevent_same_question_for_turns'] = get_string('field_required', 'capquiz');
        }
        return $validation_errors;
    }

}
