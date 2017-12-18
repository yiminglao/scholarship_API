<?php
/**
 * Created by PhpStorm.
 * User: kyler_000
 * Date: 11/1/2017
 * Time: 8:30 AM
 */

namespace Scholarship\Models;

use \Scholarship\Utilities\DatabaseConnection as DatabaseConnection;
use \Scholarship\Models\Question as Question;

class Scholarship implements \JsonSerializable
{
    private $scholarshipId;
    private $name;
    private $amount;
    private $timeframeId;
    private $isActive;
    private $questions;
    private $db;
    private $gender;
    private $usCitizenship;
    private $minMajorGpa;
    private $maxMajorGpa;
    private $minGpa;
    private $maxGpa;


    //TODO: Add functions to support other endpoints
    //TODO: Whatever else may be needed to play nicely with other endpoints


     public function __construct(...$args)
       {
           $this->db = DatabaseConnection::getInstance();
           $this->questions = [];
           switch(count($args)) {
               case 1:
                   self::__construct1($args[0]);
                   break;

               case 5:
                   self::__construct5(...$args);
                   break;
           }
       }

       function __construct1($scholarshipId)
       {
           //Set scholarshipId
           $this->scholarshipId = $scholarshipId;
           $this->questions = $this->getQuestionsFromDB();
           //Populate object
           $this->populateFromId($scholarshipId);
       }


        function __construct5($scholarship_id, $name, $amount, $timeframeid, $isActive)
        {
            $this->scholarshipId = $scholarship_id;
            $this->name = $name;
            $this->amount = $amount;
            $this->timeframeId = $timeframeid;
            $this->isActive = $isActive;
            $this->questions = null;
            $this->questions = $this->getQuestionsFromDB();
        }

    //Returns JSON encoded scholarship data
    public function JsonSerialize() {
//       $questionArr = array();

//       foreach ($this->questions as $q){
//           array_push($questionArr, $q->JsonSerialize());
//        }
        $json = [

            'scholarshipId' => $this->scholarshipId,
            'name' => $this->name,
            'amount' => $this->amount,
            'timeframeId' => $this->timeframeId,
            'isActive' => $this->isActive,
            'gender' => $this->gender,
            'usCitizenship' => $this->usCitizenship,
            'minMajorGpa' => $this->minMajorGpa,
            'maxMajorGpa' => $this->maxMajorGpa,
            'minGpa' => $this->minGpa,
            'maxGpa' => $this->maxGpa,
            'questions' => $this->questions
        ];
        return $json;
    }


    public function getAllScholarships()
    {

        return $this->allScholarships();
    }


    public function getScholarshipArray()
    {
        return [
            'scholarshipId' => $this->scholarshipId,
            'name' => $this->name,
            'amount' => $this->amount,
            'timeframeId' => $this->timeframeId,
            'isActive' => $this->isActive
        ];
    }

    /**
     * @return mixed
     */
    public function getScholarshipId() : int
    {
        return $this->scholarshipId;
    }

    /**
     * @return mixed
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getAmount() : int
    {
        return $this->amount;
    }

    /**
     * @return mixed
     */
    public function getTimeframeId() : int
    {
        return $this->timeframeId;
    }

    /**
     * @return mixed
     */
    public function getIsActive() : bool
    {
        return $this->isActive;
    }

    /**
     * @param mixed
     */
    public function setScholarshipId($scholarshipId)
    {
        $this->scholarshipId = $scholarshipId;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param mixed $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @param mixed $timeframeId
     */
    public function setTimeframeId($timeframeId)
    {
        $this->timeframeId = $timeframeId;
    }

    /**
     * @param mixed $isActive
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }

    /**
     * @return mixed
     */
    public function getQuestions()
    {
        return $this->questions;
    }

    /**
     * @param mixed $questions
     */
    public function setQuestions($questions)
    {
        $this->questions = $questions;
    }


    public function set($field, $value) {
        if (property_exists($this, $field)) {
            $this->$field = $value;
            return true;
        }
        return false;
    }

    public function delete(): bool {
        $sqlStatement = $this->db->prepare('UPDATE `scholarship` SET is_active = FALSE WHERE scholarship_id = :scholarshipID');

        $sqlStatement->bindParam(":scholarshipID", $this->scholarshipId);

        return $sqlStatement->execute();
    }

    public function save(): bool {
        if (isset($this->scholarshipId)) {
            return $this->update();
        }
        return $this->create();
    }

    private function update(): bool {
        $sqlStatement = $this->db->prepare('UPDATE `scholarship` 
            SET name = :name, amount = :amount, timeframe_id = :timeframe_id, gender = :gender,
              us_citizenship = :us_citizenship, min_major_gpa = :min_major_gpa, max_major_gpa = :max_major_gpa,
              min_gpa = :min_gpa, max_gpa = :max_gpa 
            WHERE scholarship_id = :scholarship_id');

        $sqlStatement->bindParam(":name", $this->name);
        $sqlStatement->bindParam(":amount", $this->amount);
        $sqlStatement->bindParam(":timeframe_id", $this->timeframeId);
        $sqlStatement->bindParam(":gender", $this->gender);
        $sqlStatement->bindParam(":us_citizenship", $this->usCitizenship);
        $sqlStatement->bindParam(":min_major_gpa", $this->minMajorGpa);
        $sqlStatement->bindParam(":max_major_gpa", $this->maxMajorGpa);
        $sqlStatement->bindParam(":min_gpa", $this->minGpa);
        $sqlStatement->bindParam(":max_gpa", $this->maxGpa);
        $sqlStatement->bindParam(":scholarship_id", $this->scholarshipId);

        return $sqlStatement->execute() && $this->deleteQuestions() && $this->createQuestions();
    }

    private function create(): bool {
        if (!isset($this->timeframeId)) {
            throw new \Exception('Required field "timeframeId" was not provided.');
        }
        if (!isset($this->amount)) {
            throw new \Exception('Required field "amount" was not provided.');
        }
        if (!isset($this->name)) {
            throw new \Exception('Required field "name" was not provided.');
        }
        if (!isset($this->gender)) {
            $this->gender = 'A';
        }
        if (!isset($this->usCitizenship)) {
            $this->usCitizenship = 0;
        }

        $sqlStatement = $this->db->prepare('INSERT INTO `scholarship` 
          (name, amount, timeframe_id, gender, us_citizenship, min_major_gpa, max_major_gpa, min_gpa, max_gpa)
          VALUES (:name, :amount, :timeframe_id, :gender, :us_citizenship, :min_major_gpa, :max_major_gpa, :min_gpa, :max_gpa)');

        $sqlStatement->bindParam(":name", $this->name);
        $sqlStatement->bindParam(":amount", $this->amount);
        $sqlStatement->bindParam(":timeframe_id", $this->timeframeId);
        $sqlStatement->bindParam(":gender", $this->gender);
        $sqlStatement->bindParam(":us_citizenship", $this->usCitizenship);
        $sqlStatement->bindParam(":min_major_gpa", $this->minMajorGpa);
        $sqlStatement->bindParam(":max_major_gpa", $this->maxMajorGpa);
        $sqlStatement->bindParam(":min_gpa", $this->minGpa);
        $sqlStatement->bindParam(":max_gpa", $this->maxGpa);

        if ($sqlStatement->execute()) {
            $this->scholarshipId = $this->db->lastInsertId();
            return $this->createQuestions();
        }

        return false;
    }

    private function createQuestions(): bool {
        if (empty($this->questions)) {
            return true;
        }
        $sql = 'INSERT INTO `question` (question, scholarship_id) VALUES (:question0, :scholarship_id)';
        for ($i = 1; $i < count($this->questions); $i++) {
            $sql .= ', (:question' . $i . ', :scholarship_id)';
        }

        $sqlStatement = $this->db->prepare($sql);

        foreach ($this->questions as $index => $question) {
            $sqlStatement->bindValue(':question' . $index, $question->getQuestion());
        }
        $sqlStatement->bindParam(':scholarship_id', $this->scholarshipId);

        return $sqlStatement->execute();
    }

    private function deleteQuestions(): bool {
        $sqlStatement = $this->db->prepare('DELETE FROM `question` WHERE scholarship_id = :scholarshipID');

        $sqlStatement->bindParam(":scholarshipID", $this->scholarshipId);

        return $sqlStatement->execute();
    }

    //----------------------------------------------------------------------------------
    //Query functions
    private function populateFromId($scholarshipId) {
        //Build database query
        //Template
        $stmSelect = $this->db->prepare('SELECT * FROM `scholarship` 
          WHERE `scholarship_id` = :scholarshipId
          AND `is_active` = 1');

        //Bind
        $stmSelect->bindParam('scholarshipId', $scholarshipId);

        $stmSelect->setFetchMode(\PDO::FETCH_ASSOC);

        //Execute
        $stmSelect->execute();

        //Fetch results
        $results = $stmSelect->fetch();

        if ($results) {
            //If scholarship was found in the database, populate model
            $this->name = $results['name'];
            $this->amount = $results['amount'];
            $this->isActive = $results['is_active'];
            $this->timeframeId = $results['timeframe_id'];
            $this->gender = $results['gender'];
            $this->usCitizenship = $results['us_citizenship'];
            $this->minMajorGpa = $results['min_major_gpa'];
            $this->maxMajorGpa = $results['max_major_gpa'];
            $this->minGpa = $results['min_gpa'];
            $this->maxGpa = $results['max_gpa'];
        }
        else{
            //Scholarship wasn't found in the data base
            http_response_code(\Scholarship\Http\StatusCodes::BAD_REQUEST);
            die("Scholarship not found");
        }
    }
    private function getQuestionsFromDB($scholarshipId = null){
        $this->questions = array();
        //Build database query
        //Template
        $stmSelect = $this->db->prepare("SELECT * FROM `question` WHERE `scholarship_id` = :scholarshipId");

        if ($scholarshipId == null) {
            $scholarshipId = $this->scholarshipId;
        }

        //Bind
        $stmSelect->bindParam('scholarshipId', $scholarshipId);

        $stmSelect->setFetchMode(\PDO::FETCH_ASSOC);

        //Execute
        $stmSelect->execute();

        $results = $stmSelect->fetchALL();

        //Fetch results
        if (!empty($results)) {
            $questions = array();

            foreach ($results as $question)
            {
                array_push($questions, new Question($question['question_id'], $question['question'])); //incorperate kyles methods
            }

            return $questions;
        }
    }


    private function allScholarships()
    {
        $stmSelect = $this->db->prepare("SELECT * FROM `scholarship` WHERE `is_active` = 1");

        $stmSelect->setFetchMode(\PDO::FETCH_ASSOC);

        $stmSelect->execute();

        $results = $stmSelect->fetchALL();

        if (!empty($results)) {
            $arrayOfScholarship = array();

            foreach ($results as $scholarshipArray)
            {
                $scholarship = new Scholarship($scholarshipArray['scholarship_id'], $scholarshipArray['name'],
                    $scholarshipArray['amount'], $scholarshipArray['timeframe_id'], $scholarshipArray['is_active']);
                $scholarship->setQuestions($this->getQuestionsFromDB($scholarshipArray['scholarship_id']));
                array_push($arrayOfScholarship, $scholarship); //incorperate kyles methods
            }
            return $arrayOfScholarship;
        }
        else
        {
            //Scholarship wasn't found in the data base
            http_response_code(\Scholarship\Http\StatusCodes::BAD_REQUEST);
            die("Scholarship not found");
        }
    }

}