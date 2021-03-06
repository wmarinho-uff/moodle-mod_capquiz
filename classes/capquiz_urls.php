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

/**
 * @package     mod_capquiz
 * @author      Aleksander Skrede <aleksander.l.skrede@ntnu.no>
 * @copyright   2018 NTNU
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class capquiz_urls {

    public static $param_id = 'id';
    public static $param_course_module_id = 'cmid';
    public static $param_rating = 'rating';
    public static $param_attempt = 'attempt';
    public static $param_target_url = 'target-url';
    public static $param_question_id = 'question-id';
    public static $param_question_page = 'qpage';
    public static $param_delete_selected = 'deleteselected';
    public static $param_question_list_id = 'question-list-id';

    public static $url_view = '/mod/capquiz/view.php';
    public static $url_async = '/mod/capquiz/async.php';
    public static $url_error = '/mod/capquiz/error.php';
    public static $url_action = '/mod/capquiz/action.php';
    public static $url_view_classlist = '/mod/capquiz/view_classlist.php';
    public static $url_view_configuration = '/mod/capquiz/view_configuration.php';
    // TODO: If Moodle fixes their forms, change this to view_question_list.php
    public static $url_view_question_list = '/mod/capquiz/edit.php';
    public static $url_view_badge_configuration = '/mod/capquiz/view_badge_configuration.php';
    public static $url_view_create_question_list = '/mod/capquiz/view_create_question_list.php';
    public static $url_view_matchmaking_configuration = '/mod/capquiz/view_matchmaking_configuration.php';
    public static $url_view_rating_system_configuration = '/mod/capquiz/view_rating_system_configuration.php';

    public static function redirect(\moodle_url $target) {
        $url = capquiz_urls::create_view_url(capquiz_urls::$url_action);
        $url->param(capquiz_actions::$parameter, capquiz_actions::$redirect);
        $url->param(capquiz_urls::$param_target_url, $target->out_as_local_url());
        return $url;
    }

    /**
     * @throws \coding_exception
     */
    public static function require_course_module_id_param() {
        $id = optional_param(capquiz_urls::$param_id, 0, PARAM_INT);
        if ($id !== 0) {
            return $id;
        }
        return required_param(capquiz_urls::$param_course_module_id, PARAM_INT);
    }

    public static function view_url() {
        return capquiz_urls::create_view_url(capquiz_urls::$url_view);
    }

    public static function view_question_list_url(int $question_page = 0) {
        $url = capquiz_urls::create_view_url(capquiz_urls::$url_view_question_list);
        $url->param(capquiz_urls::$param_question_page, $question_page);
        return $url;
    }

    public static function view_matchmaking_configuration_url() {
        $url = capquiz_urls::create_view_url(capquiz_urls::$url_view_matchmaking_configuration);
        return $url;
    }

    public static function view_rating_system_configuration_url() {
        $url = capquiz_urls::create_view_url(capquiz_urls::$url_view_rating_system_configuration);
        return $url;
    }

    public static function view_badge_configuration_url() {
        $url = capquiz_urls::create_view_url(capquiz_urls::$url_view_badge_configuration);
        return $url;
    }

    public static function view_classlist_url() {
        return capquiz_urls::create_view_url(capquiz_urls::$url_view_classlist);
    }

    public static function view_configuration_url() {
        return capquiz_urls::create_view_url(capquiz_urls::$url_view_configuration);
    }

    public static function view_create_question_list_url() {
        return capquiz_urls::create_view_url(capquiz_urls::$url_view_create_question_list);
    }

    public static function add_question_to_list_url(int $question_id) {
        $url = capquiz_urls::create_view_url(capquiz_urls::$url_action);
        $url->param(capquiz_actions::$parameter, capquiz_actions::$add_question_to_list);
        $url->param(capquiz_urls::$param_question_id, $question_id);
        return $url;
    }

    public static function remove_question_from_list_url(int $question_id) {
        $url = capquiz_urls::create_view_url(capquiz_urls::$url_action);
        $url->param(capquiz_actions::$parameter, capquiz_actions::$remove_question_from_list);
        $url->param(capquiz_urls::$param_question_id, $question_id);
        return $url;
    }

    public static function question_list_publish_url(capquiz_question_list $question_list) {
        $url = capquiz_urls::create_view_url(capquiz_urls::$url_action);
        $url->param(capquiz_urls::$param_question_list_id, $question_list->id());
        $url->param(capquiz_actions::$parameter, capquiz_actions::$publish_question_list);
        return $url;
    }

    public static function question_list_create_template_url(capquiz_question_list $question_list) {
        $url = capquiz_urls::create_view_url(capquiz_urls::$url_action);
        $url->param(capquiz_urls::$param_question_list_id, $question_list->id());
        $url->param(capquiz_actions::$parameter, capquiz_actions::$create_question_list_template);
        return $url;
    }

    public static function question_list_select_url(capquiz_question_list $question_list) {
        $url = capquiz_urls::create_view_url(capquiz_urls::$url_action);
        $url->param(capquiz_actions::$parameter, capquiz_actions::$set_question_list);
        $url->param(capquiz_urls::$param_question_list_id, $question_list->id());
        return $url;
    }

    public static function set_question_rating_url(int $question_id) {
        $url = capquiz_urls::create_view_url(capquiz_urls::$url_action);
        $url->param(capquiz_urls::$param_question_id, $question_id);
        $url->param(capquiz_actions::$parameter, capquiz_actions::$set_question_rating);
        return $url;
    }

    public static function response_submit_url(capquiz_question_attempt $attempt) {
        $url = capquiz_urls::create_view_url(capquiz_urls::$url_async);
        $url->param(capquiz_urls::$param_attempt, $attempt->id());
        $url->param(capquiz_actions::$parameter, capquiz_actions::$attempt_answered);
        return $url;
    }

    public static function response_reviewed_url(capquiz_question_attempt $attempt) {
        $url = capquiz_urls::create_view_url(capquiz_urls::$url_async);
        $url->param(capquiz_urls::$param_attempt, $attempt->id());
        $url->param(capquiz_actions::$parameter, capquiz_actions::$attempt_reviewed);
        return $url;
    }

    public static function create_page_url(string $relative_url) {
        global $CFG;
        $url = new \moodle_url($CFG->wwwroot . $relative_url);
        return $url;
    }

    /**
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    public static function create_view_url(string $relative_url) {
        global $CFG;
        $url = new \moodle_url($CFG->wwwroot . $relative_url);
        $url->param(capquiz_urls::$param_id, capquiz_urls::require_course_module_id_param());
        return $url;
    }
}