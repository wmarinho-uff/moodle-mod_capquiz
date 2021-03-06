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

namespace mod_capquiz\bank;

use \core_question\bank\checkbox_column;
use \core_question\bank\creator_name_column;
use \core_question\bank\delete_action_column;
use \core_question\bank\preview_action_column;
use \core_question\bank\question_name_column;
use \core_question\bank\question_type_column;
use \core_question\bank\search\tag_condition as tag_condition;
use \core_question\bank\search\hidden_condition as hidden_condition;
use \core_question\bank\search\category_condition;

defined('MOODLE_INTERNAL') || die();

/**
 * @package     mod_capquiz
 * @author      Aleksander Skrede <aleksander.l.skrede@ntnu.no>
 * @copyright   2018 NTNU
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_bank_view extends \core_question\bank\view {

    protected function wanted_columns() : array {
        $this->requiredcolumns = [
            new add_question_to_list_column($this),
            new checkbox_column($this),
            new question_type_column($this),
            new question_name_column($this),
            new creator_name_column($this),
            new delete_action_column($this),
            new preview_action_column($this)
        ];
        return $this->requiredcolumns;
    }

    public function render(string $tabname, int $page, int $perpage, string $category, bool $show_subcategories, bool $showhidden, bool $showquestiontext, array $tagids = []) : string {
        global $PAGE;
        if ($this->process_actions_needing_ui()) {
            return '';
        }
        ob_start();
        $contexts = $this->contexts->having_one_edit_tab_cap($tabname);
        list($categoryid, $contextid) = explode(',', $category);
        $catcontext = \context::instance_by_id($contextid);
        $thiscontext = $this->get_most_specific_context();
        $this->display_question_bank_header();
        $this->add_searchcondition(new tag_condition([$catcontext, $thiscontext], $tagids));
        $this->add_searchcondition(new hidden_condition(!$showhidden));
        $this->add_searchcondition(new category_condition($category, $show_subcategories, $contexts, $this->baseurl, $this->course));
        $this->display_options_form($showquestiontext, $this->baseurl->get_path());
        $this->display_question_list(
            $contexts,
            $this->baseurl,
            $category,
            $this->cm,
            false,
            $page,
            $perpage,
            $showhidden,
            $showquestiontext,
            $this->contexts->having_cap('moodle/question:add')
        );
        $PAGE->requires->js_call_amd('core_question/edit_tags', 'init', ['#questionscontainer']);
        return ob_get_clean();
    }

}
