<?php
/**
 * Created by PhpStorm.
 * User: Monkey Park
 * Date: 11/6/2017
 * Time: 9:04 AM
 */
namespace Scholarship\Models;
use \Scholarship\Utilities\DatabaseConnection;

class CompositeRating implements \JsonSerializable
{
    private $studentID;
    private $scholarshipID;
    private $score;

    public function __construct($studentID,$ScholarshipID)
    {
        $this->studentID = $studentID;
        $this->scholarshipID = $ScholarshipID;
        $this->score = self::getCompositeRating();
    }

    public function jsonSerialize()
    {
        return
            array(
                "studentID" => $this->studentID,
                "scholarshipID" => $this->scholarshipID,
                "score" => $this->score
            );
    }

    public function setStudentId($studentId) : int
    {
        $this->studentID = $studentId;
    }
    public function setScholarshipID($ScholarshipID)
    {
        $this->scholarshipID = $ScholarshipID;
    }


    public function getScore()
    {
        return $this->score;
    }

    public function getStudentId() : int
    {
        return $this->studentID;
    }

    public function getScholarshipID() : int
    {
        return $this->scholarshipID;
    }

    private function getCompositeRating()
    {
        $dbh = DatabaseConnection::getInstance();
        $stmt = $dbh->prepare("SELECT AVG (Rating) as rating FROM (SELECT Rating FROM StudentRatings WHERE StudentID = :studentID UNION ALL 
                              SELECT Rating FROM ApplicationRatings WHERE StudentID = :studentID AND ScholarshipID = :scholarshipID ) scores ");
        $stmt->bindParam('studentID', $this->studentID);
        $stmt->bindParam('scholarshipID', $this->ScholarshipID);
        $stmt->setFetchMode(\PDO::FETCH_NUM);
        $stmt->execute();
        $returnVal=$stmt->fetchAll();

        if($returnVal)
        {
            return $returnVal;

        }else{
            http_response_code(\Scholarship\Http\StatusCodes::BAD_REQUEST);
            die("No rating found");
        }
    }



}
