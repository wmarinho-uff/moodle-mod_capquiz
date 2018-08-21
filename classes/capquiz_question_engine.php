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

defined('MOODLE_INTERNAL') || die();

/**
 * @package     mod_capquiz
 * @author      Aleksander Skrede <aleksander.l.skrede@ntnu.no>
 * @copyright   2018 NTNU
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class capquiz_question_engine {
    private $capquiz;
    private $question_usage;
    private $matchmaking_loader;
    private $rating_system_loader;

    public function __construct(capquiz $capquiz, \question_usage_by_activity $question_usage, capquiz_matchmaking_strategy_loader $strategy_loader, capquiz_rating_system_loader $rating_system_loder) {
        $this->capquiz = $capquiz;
        $this->question_usage = $question_usage;
        $this->matchmaking_loader = $strategy_loader;
        $this->rating_system_loader = $rating_system_loder;
    }

    public function user_is_completed(capquiz_user $user) {
        if ($attempt = capquiz_question_attempt::active_attempt($this->capquiz, $user)) {
            return false;
        }
        if ($question = $this->find_question_for_user($user)) {
            return false;
        }
        return true;
    }

    public function attempt_for_user(capquiz_user $user) {
        if ($attempt = capquiz_question_attempt::active_attempt($this->capquiz, $user)) {
            return $attempt;
        }
        return $this->new_attempt_for_user($user);
    }

    public function attempt_for_current_user() {
        return $this->attempt_for_user($this->capquiz->user());
    }

    public function attempt_answered(capquiz_user $user, capquiz_question_attempt $attempt) {
        $rating_system = $this->rating_system_loader->rating_system();
        $attempt->mark_as_answered();
        $question = $this->capquiz->question_list()->question($attempt->question_id());
        if ($attempt->is_correctly_answered()) {
            $rating_system->update_user_rating($user, $question, 1);
            $this->set_new_highest_level_if_attained($user);
        } else {
            $rating_system->update_user_rating($user, $question, 0);
        }
        if ($previous_attempt = capquiz_question_attempt::previous_attempt($this->capquiz, $user)) {
            $this->update_question_rating($previous_attempt, $attempt);
        }
    }

    private function set_new_highest_level_if_attained(capquiz_user $user) {
        $list = $this->capquiz->question_list();
        for ($level = $list->level_count(); $level > 0; $level--) {
            $required = $list->level_rating($level);
            if ($user->rating() >= $required) {
                if ($user->highest_level() < $level) {
                    $user->set_highest_level($level);
                    break;
                }
            }
        }
    }

    public function attempt_reviewed(capquiz_user $user, capquiz_question_attempt $attempt) {
        $attempt->mark_as_reviewed();
    }

    private function new_attempt_for_user(capquiz_user $user) {
        if ($question = $this->find_question_for_user($user)) {
            return capquiz_question_attempt::create_attempt($this->capquiz, $user, $question);
        }
        return null;
    }

    private function find_question_for_user(capquiz_user $user) {
        $question_selector = $this->matchmaking_loader->selector();
        return $question_selector->next_question_for_user($user, $this->capquiz->question_list(), capquiz_question_attempt::inactive_attempts($this->capquiz, $user));
    }

    private function update_question_rating(capquiz_question_attempt $previous, capquiz_question_attempt $current) {
        $rating_system = $this->rating_system_loader->rating_system();
        $current_correct = $current->is_correctly_answered();
        $previous_correct = $previous->is_correctly_answered();
        $current_question = $this->capquiz->question_list()->question($current->question_id());
        $previous_question = $this->capquiz->question_list()->question($previous->question_id());
        if ($previous_correct && !$current_correct) {
            $rating_system->question_victory_ratings($current_question, $previous_question);
        } else {
            if (!$previous_correct && $current_correct) {
                $rating_system->question_victory_ratings($previous_question, $current_question);
            }
        }
    }

}
