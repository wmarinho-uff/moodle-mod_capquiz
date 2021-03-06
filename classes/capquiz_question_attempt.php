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
class capquiz_question_attempt {

    /** @var \stdClass $db_entry */
    private $db_entry;

    /** @var \question_usage_by_activity $question_usage */
    private $question_usage;

    public function __construct(\question_usage_by_activity $question_usage, \stdClass $db_entry) {
        $this->db_entry = $db_entry;
        $this->question_usage = $question_usage;
    }

    public static function create_attempt(capquiz $capquiz, capquiz_user $user, capquiz_question $question) /*: ?capquiz_question_attempt*/ {
        $question_usage = $capquiz->question_usage();
        $questions = question_load_questions([$question->question_id()]);
        $target_question = reset($questions);
        if (!$target_question) {
            return null;
        }
        $question_definition = \question_bank::make_question($target_question);
        $slot = $question_usage->add_question($question_definition);
        $question_usage->start_question($slot);
        \question_engine::save_questions_usage_by_activity($question_usage);
        return self::insert_attempt_entry($capquiz, $user, $question, $slot);
    }

    public static function active_attempt(capquiz $capquiz, capquiz_user $user) /*: ?capquiz_question_attempt*/ {
        global $DB;
        $criteria = [
            database_meta::$field_user_id => $user->id(),
            database_meta::$field_reviewed => false
        ];
        try {
            $entry = $DB->get_record(database_meta::$table_capquiz_attempt, $criteria, '*', MUST_EXIST);
            return new capquiz_question_attempt($capquiz->question_usage(), $entry);
        } catch (\dml_exception $e) {
            return null;
        }
    }

    public static function load_attempt(capquiz $capquiz, capquiz_user $user, int $attempt_id) /*: ?capquiz_question_attempt*/ {
        global $DB;
        $criteria = [
            database_meta::$field_id => $attempt_id,
            database_meta::$field_user_id => $user->id()
        ];
        try {
            $entry = $DB->get_record(database_meta::$table_capquiz_attempt, $criteria, '*', MUST_EXIST);
            return new capquiz_question_attempt($capquiz->question_usage(), $entry);
        } catch (\dml_exception $e) {
            return null;
        }
    }

    public static function previous_attempt(capquiz $capquiz, capquiz_user $user) /*: ?capquiz_question_attempt*/ {
        global $DB;
        $table = database_meta::$table_capquiz_attempt;
        $target_field = database_meta::$field_user_id;
        $sort_field = database_meta::$field_time_reviewed;
        $userid = $user->id();
        $sql = "SELECT * FROM {" . $table . "} WHERE $target_field=$userid";
        $sql .= " ORDER BY $sort_field DESC LIMIT 1;";
        try {
            $question_db_entry = $DB->get_record_sql($sql, null, MUST_EXIST);
            return new capquiz_question_attempt($capquiz->question_usage(), $question_db_entry);
        } catch (\dml_exception $e) {
            return null;
        }
    }

    public static function inactive_attempts(capquiz $capquiz, capquiz_user $user) : array {
        global $DB;
        $records = [];
        $criteria = [
            database_meta::$field_user_id => $user->id(),
            database_meta::$field_answered => true,
            database_meta::$field_reviewed => true
        ];
        foreach ($DB->get_records(database_meta::$table_capquiz_attempt, $criteria) as $entry) {
            array_push($records, new capquiz_question_attempt($capquiz->question_usage(), $entry));
        }
        return $records;
    }

    public function id() : int {
        return $this->db_entry->id;
    }

    public function moodle_attempt_id() : int {
        return $this->db_entry->attempt_id;
    }

    public function question_id() : int {
        return $this->db_entry->question_id;
    }

    public function question_slot() : int {
        return $this->db_entry->slot;
    }

    public function question_usage() : \question_usage_by_activity {
        return $this->question_usage;
    }

    public function is_answered() : bool {
        return $this->db_entry->answered;
    }

    public function is_correctly_answered() : bool {
        if (!$this->is_answered()) {
            return false;
        }
        $moodle_attempt = $this->question_usage->get_question_attempt($this->question_slot());
        return $moodle_attempt->get_state()->is_correct();
    }

    public function is_reviewed() : bool {
        return $this->db_entry->reviewed;
    }

    public function is_pending() : bool {
        return !$this->is_reviewed();
    }

    public function mark_as_answered() : bool {
        global $DB;
        $submitteddata = $this->question_usage->extract_responses($this->question_slot(), $_POST);
        $this->question_usage->process_action($this->question_slot(), $submitteddata);
        $db_entry = $this->db_entry;
        $db_entry->answered = true;
        $db_entry->time_answered = time();
        $this->question_usage->finish_question($this->question_slot(), time());
        \question_engine::save_questions_usage_by_activity($this->question_usage);
        try {
            $DB->update_record(database_meta::$table_capquiz_attempt, $db_entry);
            $this->db_entry = $db_entry;
            return true;
        } catch (\dml_exception $e) {
            return false;
        }
    }

    public function mark_as_reviewed() : bool {
        global $DB;
        $db_entry = $this->db_entry;
        $db_entry->reviewed = true;
        $db_entry->time_reviewed = time();
        try {
            $DB->update_record(database_meta::$table_capquiz_attempt, $db_entry);
            $this->db_entry = $db_entry;
            return true;
        } catch (\dml_exception $e) {
            return false;
        }
    }

    private static function insert_attempt_entry(capquiz $capquiz, capquiz_user $user, capquiz_question $question, int $slot) /*: ?capquiz_question_attempt*/ {
        global $DB;
        $attempt_entry = new \stdClass();
        $attempt_entry->slot = $slot;
        $attempt_entry->user_id = $user->id();
        $attempt_entry->question_id = $question->id();
        try {
            $DB->insert_record(database_meta::$table_capquiz_attempt, $attempt_entry);
            return self::active_attempt($capquiz, $user);
        } catch (\dml_exception $e) {
            return null;
        }
    }

}