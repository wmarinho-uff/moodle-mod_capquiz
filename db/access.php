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

defined('MOODLE_INTERNAL') || die();

/**
 * @package     mod_capquiz
 * @author      Aleksander Skrede <aleksander.l.skrede@ntnu.no>
 * @copyright   2018 NTNU
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$capabilities = [
    'mod/capquiz:instructor' => [
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,
        'legacy' => [
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW
        ]
    ],
    'mod/capquiz:student' => [
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,
        'legacy' => [
            'student' => CAP_ALLOW,
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW
        ]
    ],
    'mod/capquiz:addinstance' => [
        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => [
            'editingteacher' => CAP_ALLOW,
            'coursecreator' => CAP_ALLOW,
            'manager' => CAP_ALLOW
        ]
    ]
];

//$capabilities = [    // Can start a quiz and move on to the next question.
//    // NB: must have 'attempt' as well to be able to see the questions.
//    'mod/capquiz:instructor' => [
//        'captype'      => 'write',
//        'contextlevel' => CONTEXT_MODULE,
//        'legacy'       => [
//            'teacher'        => CAP_ALLOW,
//            'editingteacher' => CAP_ALLOW,
//            'manager'        => CAP_ALLOW
//        ]
//    ],
//
//    'mod/capquiz:student' => [
//            'editingteacher' => CAP_ALLOW,
//            'manager'        => CAP_ALLOW
//        ]
//    ],
//
//    // Can try to answer the quiz.
//    'mod/capquiz:attempt' => [
//        'captype'      => 'write',
//        'contextlevel' => CONTEXT_MODULE,
//        'legacy'       => [
//            'student'        => CAP_ALLOW,
//            'teacher'        => CAP_ALLOW,
//            'editingteacher' => CAP_ALLOW,
//            'manager'        => CAP_ALLOW
//        ]
//    ],
//
//    // Can see who gave what answer.
//    'mod/capquiz:seeresponses' => [
//        'captype'      => 'read',
//        'contextlevel' => CONTEXT_MODULE,
//        'legacy'       => [
//            'teacher'        => CAP_ALLOW,
//            'editingteacher' => CAP_ALLOW,
//            'manager'        => CAP_ALLOW
//        ]
//    ],
//
//    // Can add / delete / update questions.
//    'mod/capquiz:editquestions' => [
//        'captype'      => 'write',
//        'contextlevel' => CONTEXT_MODULE,
//        'legacy'       => [
//            'editingteacher' => CAP_ALLOW,
//            'manager'        => CAP_ALLOW
//        ]
//    ],
//
//    // Can add an instance of this module to a course.
//    'mod/capquiz:addinstance' => [
//        'captype'      => 'write',
//        'contextlevel' => CONTEXT_COURSE,
//        'legacy'       => [
//            'editingteacher' => CAP_ALLOW,
//            'coursecreator'  => CAP_ALLOW,
//            'manager'        => CAP_ALLOW
//        ]
//    ],
//];