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

namespace mod_capquiz\form\view;

use mod_capquiz\capquiz;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

class configure_capquiz_form extends \moodleform {
    private $capquiz;

    public function __construct(capquiz $capquiz, \moodle_url $url) {
        $this->capquiz = $capquiz;
        parent::__construct($url);

    }

    public function set_capquiz(capquiz $capquiz) {
        $this->capquiz = $capquiz;
    }

    public function definition() {
        $form = $this->_form;

        $form->addElement('text', 'name', get_string('name', 'capquiz'));
        $form->setType('name', PARAM_TEXT);
        $form->setDefault('name', $this->capquiz->name());
        $form->addRule('name', get_string('name_required', 'capquiz'), 'required', null, 'client');

        $form->addElement('text', 'default_user_rating', get_string('default_user_rating', 'capquiz'));
        $form->setType('default_user_rating', PARAM_INT);
        $form->setDefault('default_user_rating', $this->capquiz->default_user_rating());
        $form->addRule('default_user_rating', get_string('default_user_rating_required', 'capquiz'), 'required', null, 'client');

        $form->addElement('text', 'default_user_k_factor', get_string('default_user_k_factor', 'capquiz'));
        $form->setType('default_user_k_factor', PARAM_INT);
        $form->setDefault('default_user_k_factor', $this->capquiz->default_user_k_factor());
        $form->addRule('default_user_k_factor', get_string('default_user_k_factor_required', 'capquiz'), 'required', null, 'client');

        $form->addElement('text', 'default_question_k_factor', get_string('default_question_k_factor', 'capquiz'));
        $form->setType('default_question_k_factor', PARAM_INT);
        $form->setDefault('default_question_k_factor', $this->capquiz->default_question_k_factor());
        $form->addRule('default_question_k_factor', get_string('default_question_k_factor_required', 'capquiz'), 'required', null, 'client');
        $this->add_action_buttons(true, 'submit');
    }

    public function validations($data, $files) {
        $validation_errors = [];
        if (empty($data['name']))
            $validation_errors['name'] = get_string('name_required', 'capquiz');
        if (empty($data['description']))
            $validation_errors['description'] = get_string('description_required', 'capquiz');
        if (empty($data['default_user_rating']))
            $validation_errors['default_user_rating'] = get_string('default_user_rating_required', 'capquiz');
        if (empty($data['default_user_k_factor']))
            $validation_errors['default_user_k_factor_required'] = get_string('default_user_rating_required', 'capquiz');
        if (empty($data['default_question_k_factor']))
            $validation_errors['default_question_k_factor'] = get_string('default_question_k_factor_required', 'capquiz');
        return $validation_errors;
    }

}