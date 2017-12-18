<?php


namespace Scholarship\Models;

use Scholarship\Utilities\DatabaseConnection as DatabaseConnection;

class Rating implements \JsonSerializable
{
    private $score;
    private $studentID;
    private $facultyID;
    private $scholarshipID;
    private $ratingTypeID;

    public function __construct($studentID, $facultyID, $scholarshipID=-1, $ratingTypeID=-1)
    {
        $this->studentID = $studentID;
        $this->facultyID = $facultyID;
        $this->scholarshipID = $scholarshipID;
        $this->ratingTypeID = $ratingTypeID;
        if($scholarshipID == -1){
            $this->score = $this->getStudentRating();
        }
        else
        {

            $this->score = $this->getApplicationRating();
        }

    }
    //getters
    public function getScore()
    {
        return $this->score;
    }

    public function getStudentID()
    {
        return $this->studentID;
    }

    public function getFacultyID()
    {
        return $this->facultyID;
    }

    public function getScholarshipID()
    {
        return $this->scholarshipID;
    }

    public function getRatingTypeID()
    {
        return $this->ratingTypeID;
    }


    function jsonSerialize()
    {
        return
            array(
                "studentID"=>$this->studentID,
                "facultyID"=>$this->facultyID,
                "scholarshipID"=>$this->scholarshipID,
                "ratingTypeID"=>$this->ratingTypeID,
                "score"=>$this->score

            );
    }
    //setters
    public function setStudentID($studentID)
    {
        $this->studentID = $studentID;
    }

    public function setFacultyID($facultyID)
    {
        $this->facultyID = $facultyID;
    }

    public function setScholarshipID($scholarshipID)
    {
        $this->scholarshipID = $scholarshipID;
    }


    public function setRatingTypeID($ratingTypeID)
    {
        $this->ratingTypeID = $ratingTypeID;
    }

    public function setScore($score)
    {
        $this->score=filter_var($score,FILTER_SANITIZE_NUMBER_INT);
        //$this->saveToDB();
    }

    //return all Application Ratings
    public function getAllRating()
    {
        $dbh = DatabaseConnection::getInstance();
        $stmt = $dbh->prepare("SELECT * FROM ApplicationRatings");
        $stmt->setFetchMode(\PDO::FETCH_ASSOC);
        $stmt->execute();
        $data = $stmt->fetchAll();
        if($data)
        {
            return $data;
        }else{
            http_response_code(\Scholarship\Http\StatusCodes::BAD_REQUEST);
            die("No rating found");
        }
    }
    //get the score from the database (Past Score)
    private function getStudentRating()
    {
        $dbh = DatabaseConnection::getInstance();
        $stmt = $dbh->prepare("SELECT Rating FROM StudentRatings WHERE StudentID = :student AND FacultyID = :faculty");
        $stmt->bindParam('student', $this->studentID);
        $stmt->bindParam('faculty', $this->facultyID);
        $stmt->setFetchMode(\PDO::FETCH_NUM);
        $executed = $stmt->execute();
        if($executed){
            $returnVal=$stmt->fetch();
            return $returnVal[0];
        } else {
            throw new \PDOException("Failed to execute SQL Query");
        }
    }
    //get the score from the database for an application
    private function getApplicationRating()
    {
        $dbh = DatabaseConnection::getInstance();
        $stmt = $dbh->prepare("SELECT Rating FROM ApplicationRatings WHERE StudentID = :student AND FacultyID = :faculty
                                AND ScholarshipID = :scholarship AND RatingTypeID = :ratingType");
        $stmt->bindParam(':student', $this->studentID);
        $stmt->bindParam(':faculty', $this->facultyID);
        $stmt->bindParam(':scholarship', $this->scholarshipID);
        $stmt->bindParam(':ratingType', $this->ratingTypeID);
        $stmt->setFetchMode(\PDO::FETCH_NUM);
        $executed = $stmt->execute();
        if($executed){
        $returnVal=$stmt->fetch();
        return $returnVal[0];
        } else {
            throw new \PDOException("Failed to execute SQL Query");
        }
    }
    //set the past score in the database
    private function setStudentRating()
    {
        try {
            $dbh = DatabaseConnection::getInstance();
            $stmt = $dbh->prepare("CALL RateStudent(:student, :faculty, :score)");
            $stmt->bindParam('student', $this->studentID);
            $stmt->bindParam('faculty', $this->facultyID);
            $stmt->bindParam('score',$this->score);
            $stmt->execute();
        } catch (\PDOException $err){
            throw $err;
        }
    }
    //set the application score in the database
    private function setApplicationRating()
    {
        try {
            $dbh = DatabaseConnection::getInstance();
            $stmt = $dbh->prepare("CALL RateApplication(:student, :faculty, :scholarship, :ratingtype, :score)");

            $stmt->bindParam('student', $this->studentID);
            $stmt->bindParam('faculty', $this->facultyID);
            $stmt->bindParam('scholarship', $this->scholarshipID);
            $stmt->bindParam('ratingtype', $this->ratingTypeID);
            $stmt->bindParam('score',$this->score);
            $stmt->execute();
        } catch (\PDOException $err){
            throw $err;
        }
    }
    //commit scores to the database
    public function saveToDB()
    {
        if($this->scholarshipID==-1){
            $this->setStudentRating();
        }
        else
            $this->setApplicationRating();
    }


}