<?php

class CreateQuiz
{

    private $dbConnection;

    public function __construct($dbConnection)
    {
        $this->dbConnection = $dbConnection;
    }

    public function createQuiz()
    {
        // create a new quiz
        try {

            if ($_SERVER['CONTENT_TYPE'] !== 'application/json') {
                $response = new Response();
                $response->setSuccess(false);
                $response->setHttpStatusCode(400);
                $response->addMessage("Content type header is not set to JSON");
                $response->send();
                exit();
            }

            $rawPOSTData = file_get_contents('php://input');

            if (!$jsonData = json_decode($rawPOSTData)) {
                $response = new Response();
                $response->setSuccess(false);
                $response->setHttpStatusCode(400);
                $response->addMessage("Request body is not valid JSON");
                $response->send();
                exit();
            }

            // Extract quiz details from JSON
            $title = $jsonData->title;
            $description = $jsonData->description;
            $questions = $jsonData->questions;


            $quiz = new Quiz($title, $description, $questions);

            $json = json_encode($quiz);
            $response = new Response();
            $response->setHttpStatusCode(201);
            $response->setSuccess(true);
            $response->addMessage("Quiz created");
            $response->setData($json);
            $response->send();
            exit();

            

            foreach ($questionsData as $questionData) {
                $questionText = $questionData->question;
                $answersData = $questionData->answers;

                // Create a new Question object
                $question = new Question($questionId, $questionText, $quizID);

                // Iterate over answers data and add them to the question
                foreach ($answersData as $answerData) {
                    $answerText = $answerData->answer;
                    $isCorrect = $answerData->is_correct;

                    // Create a new Answer object
                    $answer = new Answer($answerText, $isCorrect, $question->getQuestionID());

                    // Add the answer to the question
                    $question->addAnswer($answer);
                }

                // Add the question to the quiz
                $quiz->addQuestion($question);
            }

            $quizArray = $quiz->returnQuizAsArray();

            $response = new Response();
            $response->setHttpStatusCode(201);
            $response->setSuccess(true);
            $response->addMessage("Quiz created");
            $response->setData($quizArray);
            $response->send();
            exit();


            $this->dbConnection->beginTransaction();

            // create the quiz
            $query = $this->dbConnection->prepare('INSERT INTO quiz (title, description) VALUES (:title, :description)');
            $query->bindParam(':title', $title, PDO::PARAM_STR);
            $query->bindParam(':description', $description, PDO::PARAM_STR);
            $query->execute();

            // get the ID of the new quiz
            $quizID = $this->dbConnection->lastInsertId();

            // create the answers


            $this->dbConnection->commit();

            // $returnData = array();
            // $returnData['quiz_id'] = $quizID;
            // foreach ($questions as $question) {
            //     $returnData['questions'][] = $question['question'];
            // }
            // foreach ($answersData as $answer) {
            //     $returnData['answers'][] = $answer['answer'];
            // }

            $response = new Response();
            $response->setHttpStatusCode(201);
            $response->setSuccess(true);
            $response->addMessage("Quiz created");
            $response->send();
        } catch (PDOException $ex) {
            // we create a rollback in case of an error
            $this->dbConnection->rollBack();
            error_log("Database query error - " . $ex, 0);
            $response = new Response();
            $response->setHttpStatusCode(500);
            $response->setSuccess(false);
            $response->addMessage("Failed to create quiz");
            $response->send();
            exit;
        }
    }

    // public function createQuiz()
    // {
    //     // create a new quiz
    //     try {

    //         if ($_SERVER['CONTENT_TYPE'] !== 'application/json') {
    //             $response = new Response();
    //             $response->setSuccess(false);
    //             $response->setHttpStatusCode(400);
    //             $response->addMessage("Content type header is not set to JSON");
    //             $response->send();
    //             exit();
    //         }

    //         $rawPOSTData = file_get_contents('php://input');

    //         if (!$jsonData = json_decode($rawPOSTData)) {
    //             $response = new Response();
    //             $response->setSuccess(false);
    //             $response->setHttpStatusCode(400);
    //             $response->addMessage("Request body is not valid JSON");
    //             $response->send();
    //             exit();
    //         }

    //            // Extract quiz details from JSON
    //     $title = $jsonData->title;
    //     $description = $jsonData->description;
    //     $questions = $jsonData->questions;
    //     // $answers = $jsonData->answers;

    //     $returnData = array();
    //     $returnData['title'] = $title;
    //     $returnData['description'] = $description;
    //     $returnData['questions'] = $questions;
    //     // $returnData['answers'] = $answers;


    //     // $response = new Response();
    //     // $response->setHttpStatusCode(201);
    //     // $response->setSuccess(true);
    //     // $response->addMessage("Quiz created");
    //     // $response->setData($returnData);
    //     // $response->send();
    //     // exit();

    //         $quiz = new Quiz(
    //             $jsonData->title,
    //             $jsonData->description,
    //             $jsonData->questions
    //         );

    //         $title = $quiz->getTitle();
    //         $description = $quiz->getDescription();
    //         $questions = $quiz->getQuestions();
    //         $answers = $quiz->getAnswers();


    //         $this->dbConnection->beginTransaction();

    //         // create the quiz
    //         $query = $this->dbConnection->prepare('INSERT INTO quiz (title, description) VALUES (:title, :description)');
    //         $query->bindParam(':title', $title, PDO::PARAM_STR);
    //         $query->bindParam(':description', $description, PDO::PARAM_STR);
    //         $query->execute();

    //         // get the ID of the new quiz
    //         $quizID = $this->dbConnection->lastInsertId();

    //         // create the answers
    //         foreach ($questions as $question) {
    //             $query = $this->dbConnection->prepare('INSERT INTO question (quiz_id, question) VALUES (:quiz_id, :question)');
    //             $query->bindParam(':quiz_id', $quizID, PDO::PARAM_INT);
    //             $query->bindParam(':question', $question['question'], PDO::PARAM_STR);
    //             $query->execute();
    //             $questionID = $this->dbConnection->lastInsertId();

    //             foreach ($answers as $answer) {
    //                 $query = $this->dbConnection->prepare('INSERT INTO answer (question_id, answer, is_correct) VALUES (:question_id, :answer, :is_correct)');
    //                 $query->bindParam(':question_id', $questionID, PDO::PARAM_INT);
    //                 $query->bindParam(':answer', $answer['answer'], PDO::PARAM_STR);
    //                 $query->bindParam(':is_correct', $answer['is_correct'], PDO::PARAM_BOOL);
    //                 $query->execute();
    //             }
    //         }

    //         $this->dbConnection->commit();

    //         $returnData = array();
    //         $returnData['quiz_id'] = $quizID;
    //         foreach ($questions as $question) {
    //             $returnData['questions'][] = $question['question'];
    //         }
    //         foreach ($answers as $answer) {
    //             $returnData['answers'][] = $answer['answer'];
    //         }

    //         $response = new Response();
    //         $response->setHttpStatusCode(201);
    //         $response->setSuccess(true);
    //         $response->addMessage("Quiz created");
    //         $response->send();
    //     } catch (PDOException $ex) {
    //         // we create a rollback in case of an error
    //         $this->dbConnection->rollBack();
    //         error_log("Database query error - " . $ex, 0);
    //         $response = new Response();
    //         $response->setHttpStatusCode(500);
    //         $response->setSuccess(false);
    //         $response->addMessage("Failed to create quiz");
    //         $response->send();
    //         exit;
    //     }
    // }
}
