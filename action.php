<?php

namespace mod_capquiz;

require_once("../../config.php");

require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot . '/mod/capquiz/lib.php');
require_once($CFG->dirroot . '/mod/capquiz/urls.php');
require_once($CFG->dirroot . '/mod/capquiz/actions.php');
require_once($CFG->dirroot . '/mod/capquiz/classes/output/instructor/question_list_create_view.php');

function redirect_to_front_page() {
    header('Location: /');
    exit;
}

function redirect_to_plugin_index(capquiz $capquiz) {
    $target_url = new \moodle_url(capquiz_urls::$url_view);
    $target_url->param(capquiz_urls::$param_id, $capquiz->course_module_id());
    redirect($target_url);
}

function set_action_url(capquiz $capquiz) {
    global $PAGE;
    $PAGE->set_context($capquiz->context());
    $PAGE->set_cm($capquiz->course_module());
    $PAGE->set_pagelayout('incourse');
    $PAGE->set_url($PAGE->url);
}

function assign_question_list(capquiz $capquiz) {
    if ($question_list_id = optional_param(capquiz_urls::$param_question_list_id, 0, PARAM_TEXT)) {
        $capquiz->assign_question_list($question_list_id);
    }
}

function create_capquiz_question(int $question_id, capquiz_question_list $list, float $rating = 600) {
    global $DB;
    $rated_question = new \stdClass();
    $rated_question->question_list_id = $list->id();
    $rated_question->question_id = $question_id;
    $rated_question->rating = $rating;
    $DB->insert_record(database_meta::$table_capquiz_question, $rated_question);
}

function add_question_to_list(capquiz $capquiz) {
    if ($question_id = optional_param(capquiz_urls::$param_question_id, 0, PARAM_TEXT)) {
        if (!$capquiz->has_question_list()) {
            if ($question_list_id = optional_param(capquiz_urls::$param_question_list_id, 0, PARAM_TEXT)) {
                create_capquiz_question($question_id, $capquiz->question_registry()->question_list($question_list_id), $capquiz->default_question_rating());
            }
        } else {
            create_capquiz_question($question_id, $capquiz->question_list());
        }
    }
}

function publish_question_list(capquiz $capquiz) {
    if (!$capquiz->publish()) {
        throw new \Exception("Unable to publish question list for CAPQuiz " . $capquiz->name());
    }
}

function set_question_rating(capquiz $capquiz) {
    $question_id = required_param(capquiz_urls::$param_question_id, PARAM_INT);
    if ($question = $capquiz->question_list()->question($question_id)) {
        if ($rating = optional_param(capquiz_urls::$param_rating, null, PARAM_FLOAT)) {
            $question->set_rating($rating);
        } else if ($rating = $_POST[capquiz_urls::$param_rating]) {
            $question->set_rating($rating);
        }
    } else {
        throw new \Exception("The specified question does not exist");
    }
}

function determine_action(capquiz $capquiz, string $action_type) {
    $capquiz->require_instructor_capability();
    if ($action_type == capquiz_actions::$action_set_question_list) {
        assign_question_list($capquiz);
    } else if ($action_type == capquiz_actions::$action_add_question_to_list) {
        add_question_to_list($capquiz);
    } else if ($action_type == capquiz_actions::$action_publish_question_list) {
        publish_question_list($capquiz);
    } else if ($action_type == capquiz_actions::$action_set_question_rating) {
        set_question_rating($capquiz);
    }
    redirect_to_plugin_index($capquiz);
}

function capquiz_action() {
    $course_module_id = optional_param(capquiz_urls::$param_cmid, null, PARAM_INT);
    $action_type = optional_param(capquiz_actions::$param_action, '', PARAM_TEXT);
    if ($course_module_id) {
        $capquiz = new capquiz($course_module_id);
        determine_action($capquiz, $action_type);
    } else {
        redirect_to_front_page();
    }
}

capquiz_action();