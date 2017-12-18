<?php
/**
 * Created by PhpStorm.
 * User: kyler_000
 * Date: 11/1/2017
 * Time: 8:30 AM
 */

namespace Scholarship\Models;
use \Scholarship\Utilities\DatabaseConnection as DatabaseConnection;


class Question  implements \JsonSerializable {
    private $questionID;
    private $question;
    private $db;

    public function __construct(...$args)
    {
        //database connection
        $this->db = DatabaseConnection::getInstance();

        switch(count($args)) {
            case 1:
                self::__construct1($args[0]);
                break;

            case 2:
                self::__construct2(...$args);
                break;
        }
    }

    private function __construct1($questionID) {
        $this->questionID = $questionID;

        //populate question
        $this->populateQuestion($questionID);
    }

    private function __construct2($questionID, $question) {
        $this->questionID = $questionID;
        $this->question = $question;
    }

    public function JsonSerialize(){
        return [
            'questionID' => $this->questionID,
            'question' => $this->question
        ];
    }
    
    /**
     * @return mixed
     */
    public function getQuestionID()
    {
        return $this->questionID;
    }

    /**
     * @param mixed $questionID
     */
    public function setQuestionID($questionID)
    {
        $this->questionID = $questionID;
    }

    /**
     * @return mixed
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * @param mixed $question
     */
    public function setQuestion($question)
    {
        $this->question = $question;
    }


    //Database query to populate question
    private function populateQuestion($questionID){

        //prepared statement
        $stmSelect = $this->db->prepare("SELECT * FROM `question` WHERE 'question_id' = :questionID");

        $stmSelect->bindParam('questionID', $questionID);

        $stmSelect->setFetchMode(\PDO::FETCH_ASSOC);

        $stmSelect->execute();

        $result = $stmSelect->fetch();

        if($result){
            $this->question = $result['question'];
        }
        else {
            //response code error
            http_response_code(\Scholarship\Http\StatusCodes::BAD_REQUEST);
            die("Question not found.");
        }
    }


}