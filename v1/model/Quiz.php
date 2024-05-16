<?php

require_once('Question.php');

class QuizException extends Exception{}

class Quiz {

    private $_id;
    private $_title;
    private $_description;
    private $_questions = array();

    public function __construct($id, $title, $description, $questions) {
        $this->setID($id);
        $this->setTitle($title);
        $this->setDescription($description);
        foreach($questions as $question) {
            $this->addQuestion($question);
        }
    }

    public function setID($id) {
        if (
            $id !== null && (!is_numeric($id)
                || $id <= 0
                || $id > 9223372036854775807)
            || $this->_id !== null
        ) {
            throw new QuizException('Quiz ID error');
        }
        $this->_id = $id;
    }

    public function setTitle($title) {
        if (strlen($title) <= 0 || strlen($title) > 255) {
            throw new QuizException('quiz title error');
        }
        $this->_title = $title;
    }

    public function setDescription($description) {
        if (($description) <= 0 || strlen($description) > 65535) {
            throw new QuizException('quiz description error');
        }
        $this->_description = $description;
    }

    public function addQuestion($question) {
        // var_dump($question);
        $this->_questions[] = new Question(null, null, $question->question, $question->option_a, $question->option_b, $question->option_c, $question->option_d, $question->correct_option);
    }

    public function removeQuestion($question) {
        $key = array_search($question, $this->_questions, true);
        if ($key === false) {
            throw new QuizException('Question not found');
        }
        unset($this->_questions[$key]);
        $this->_questions = array_values($this->_questions);
    }

    public function getID() {
        return $this->_id;
    }

    public function getTitle() {
        return $this->_title;
    }

    public function getDescription() {
        return $this->_description;
    }

    public function getQuestions() {
        return $this->_questions;
    }

    public function returnQuizAsArray() {
        $quiz = array();
        $quiz['id'] = $this->getID();;
        $quiz['title'] = $this->getTitle();
        $quiz['description'] = $this->getDescription();
        $quiz['questions'] = array_map(function($question) {
            return $question->returnQuestionAsArray();
        }, $this->_questions);

        return $quiz;
    }
}