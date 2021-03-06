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

namespace mod_capquiz\output;

use mod_capquiz\capquiz;
use mod_capquiz\capquiz_urls;
use mod_capquiz\capquiz_question_list;

defined('MOODLE_INTERNAL') || die();

require_once('../../config.php');

/**
 * @package     mod_capquiz
 * @author      Aleksander Skrede <aleksander.l.skrede@ntnu.no>
 * @copyright   2018 NTNU
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_list_renderer {
    private $capquiz;
    private $renderer;

    public function __construct(capquiz $capquiz, renderer $renderer) {
        $this->capquiz = $capquiz;
        $this->renderer = $renderer;
    }

    public function render() {
        $question_list = $this->capquiz->question_list();
        if ($question_list->has_questions()) {
            return $this->render_questions($question_list);
        }
        $title = get_string('question_list', 'capquiz');
        $no_questions = get_string('question_list_no_questions', 'capquiz');
        return "<h3>$title</h3><p>$no_questions</p>";
    }

    private function render_questions(capquiz_question_list $question_list) {
        global $PAGE;
        $PAGE->requires->js_call_amd('mod_capquiz/edit_questions', 'initialize', [
            $this->capquiz->course_module_id()
        ]);
        $rows = [];
        $questions = $question_list->questions();
        for ($i = 0; $i < $question_list->question_count(); $i++) {
            $question = $questions[$i];
            $rows[] = [
                'index' => $i + 1,
                'name' => $question->name(),
                'rating' => $question->rating(),
                'question_id' => $question->id(),
                'rating_url' => capquiz_urls::set_question_rating_url($question->id())->out(false),
                'button' => [
                    'primary' => true,
                    'method' => 'post',
                    'url' => capquiz_urls::remove_question_from_list_url($question->id())->out(false),
                    'label' => get_string('remove', 'capquiz')
                ]
            ];
        }
        $message = null;
        if ($question_list->has_questions()) {
            $message = get_string('update_rating_explanation', 'capquiz');
        }
        return $this->renderer->render_from_template('capquiz/question_list', [
            'questions' => $rows,
            'message' => $message ? $message : false
        ]);
    }
}
