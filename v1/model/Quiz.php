<?php

class QuizException extends Exception{}

class Quiz {

    public $_id;
    public $_title;
    private $_description;
    private $_questions;

    public function __construct($id, $title, $description, $questions) {
        $this->setID($id);
        $this->setTitle($title);
        $this->setDescription($description);
        foreach($questions as $question) {
            $this->addQuestion($question);
        }
    }

    private function setID($id) {
        $this->_id = $id;
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

    public function setTitle($title) {
        $this->_title = $title;
    }

    public function setDescription($description) {
        $this->_description = $description;
    }

    public function addQuestion($question) {
        $this->_questions[] = new Question($question['id'], $question['quizID'], $question['question'], $question['optionA'], $question['optionB'], $question['optionC'], $question['optionD'], $question['correctAnswer']);
    }

    public function removeQuestion($question) {
        $key = array_search($question, $this->_questions, true);
        if ($key === false) {
            throw new QuizException('Question not found');
        }
        unset($this->_questions[$key]);
        $this->_questions = array_values($this->_questions);
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
