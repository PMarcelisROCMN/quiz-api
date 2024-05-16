<?php

/* 
entry point of the file. This file is the controller for the quiz endpoint
We will create the DB connection here and call the appropriate method based on the HTTP method
*/

require_once('db.php');
require_once('../model/quiz.php');
require_once('../model/Response.php');
require_once('../handler/quiz/create.php');

// try to connect to the database
try {
    $DB = DB::connectToDB();
} catch (PDOException $ex) {
    error_log("Connection error - ".$ex, 0);
    $response = new Response();
    $response->setHttpStatusCode(500);
    $response->setSuccess(false);
    $response->addMessage("Database connection error");
    $response->send();
    exit;
}

if (array_key_exists("quizid", $_GET)){

} else if (empty($_GET)){

    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $createQuiz = new CreateQuiz($DB);
        $createQuiz->createQuiz();
    } else if ($_SERVER['REQUEST_METHOD'] === 'GET'){
        // get all the quizzes
    } else {
        $response = new Response();
        $response->setHttpStatusCode(405);
        $response->setSuccess(false);
        $response->addMessage("Method not allowed");
        $response->send();
        exit;
    }

} else {
    $response = new Response();
    $response->setHttpStatusCode(405);
    $response->setSuccess(false);
    $response->addMessage("Method not allowed");
    $response->send();
    exit;
}