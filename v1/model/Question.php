<?php

class QuestionException extends Exception
{
}

class Question
{
    private $_id;
    private $_quizID;
    private $_question;
    private $_optionA;
    private $_optionB;
    private $_optionC;
    private $_optionD;
    private $_correctAnswer;

    public function __construct($id, $quizID, $question, $optionA, $optionB, $optionC, $optionD, $correctAnswer)
    {
        $this->_id = $id;
        $this->_question = $question;
        $this->_quizID = $quizID;
        $this->_optionA = $optionA;
        $this->_optionB = $optionB;
        $this->_optionC = $optionC;
        $this->_optionD = $optionD;
        $this->_correctAnswer = $correctAnswer;
    }

    public function getQuestionID()
    {
        return $this->_id;
    }

    public function setQuestionID($id)
    {
        if (
            $id !== null && (!is_numeric($id)
                || $id <= 0
                || $id > 9223372036854775807)
            || $this->_id !== null
        ) {
            throw new QuestionException('Question ID error');
        }

        $this->_id = $id;
    }

    public function getQuestion()
    {
        return $this->_question;
    }

    public function setQuestion($question)
    {
        if (strlen($question) <= 0 || strlen($question) > 65535) {
            throw new QuestionException('question error');
        }
        $this->_question = $question;
    }

    public function getQuizID()
    {
        return $this->_quizID;
    }

    public function setQuizID($quizID)
    {
        if ($quizID !== null && (!is_numeric($quizID) || $quizID <= 0 || $quizID >  9223372036854775807) || $this->_id !== null) 
        {
            throw new QuestionException('Question ID error');
        }
        $this->_quizID = $quizID;
    }

    public function getAnswers()
    {
        return array(
            $this->_optionA,
            $this->_optionB,
            $this->_optionC,
            $this->_optionD,
            $this->_correctAnswer
        );
    }

    public function setAnswers($optionA, $optionB, $optionC, $optionD, $correctAnswer)
    {
        $options = [$optionA, $optionB, $optionC, $optionD];

        foreach ($options as $option) {
            if (strlen($option) <= 0 || strlen($option) > 255) {
                throw new QuestionException('Answers cannot be blank or more than 255 characters');
            }
        }

        $this->_optionA = $optionA;
        $this->_optionB = $optionB;
        $this->_optionC = $optionC;
        $this->_optionD = $optionD;

        if (!in_array($correctAnswer, ['A', 'B', 'C', 'D'])) {
            throw new QuestionException('Invalid correct answer');
        }
        $this->_correctAnswer = $correctAnswer;
    }

    public function returnQuestionAsArray()
    {
        $questionArray = array();
        $questionArray['id'] = $this->_id;
        $questionArray['quiz_id'] = $this->_quizID;
        $questionArray['question'] = $this->_question;
        $questionArray['optionA'] = $this->_optionA;
        $questionArray['optionB'] = $this->_optionB;
        $questionArray['optionC'] = $this->_optionC;
        $questionArray['optionD'] = $this->_optionD;
        $questionArray['correctAnswer'] = $this->_correctAnswer;
        return $questionArray;
    }
}