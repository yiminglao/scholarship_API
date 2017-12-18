<?php
/**
 * Created by PhpStorm.
 * User: colin.heald
 * Date: 10/30/2017
 * Time: 11:08 AM
 */

namespace Scholarship\Controllers;

use Scholarship\Utilities\DatabaseConnection;
use Scholarship\Models\Rating as Rating;
use Scholarship\Models\CompositeRating as CompositeRating;
use Scholarship\Models\Token as Token;
use Scholarship\Models\User as User;



class RatingController
{

    public function getCompositeRating($studentID,$ScholarshipID)
    {
        if($this->checkUserExists($studentID) == false || $this->checkScholarshipById($ScholarshipID) == false)
        {
            http_response_code(\Scholarship\Http\StatusCodes::BAD_REQUEST);

        }
        else
        {
            if($this->isFaculty())
            {
                $compositeRating = new CompositeRating($studentID,$ScholarshipID);
                return $compositeRating;
            }else{
                http_response_code(\Scholarship\Http\StatusCodes::UNAUTHORIZED);
                die();
            }
        }

    }

    public function getAllRating()
    {

        if($this->isFaculty())
        {
            $dbh = DatabaseConnection::getInstance();
            $stmt = $dbh->prepare("SELECT * FROM ApplicationRatings");
            $stmt->setFetchMode(\PDO::FETCH_ASSOC);
            $stmt->execute();
            $data = $stmt->fetchAll();
            $arr = array();
            foreach ($data as $value)
            {
                $rating = new Rating($value['StudentID'],$value['FacultyID']);
                array_push($arr,$rating);
            }
            http_response_code(\Scholarship\Http\StatusCodes::CREATED);
           return $arr;
        }else{
            http_response_code(\Scholarship\Http\StatusCodes::UNAUTHORIZED);
            die();
        }
    }

    public function getStudentPastScore($studentID, $facultyID)
    {

        if($this->checkUserExists($studentID) == false)
        {
            http_response_code(\Scholarship\Http\StatusCodes::BAD_REQUEST);

        }
        else if($this->isFaculty())
        {
        $rating = new Rating($studentID, $facultyID);
        return $rating;
        }else{
            http_response_code(\Scholarship\Http\StatusCodes::UNAUTHORIZED);
            die();
        }
    }

    public function setApplicationScore($studentID, $facultyID, $scholarshipID, $ratingTypeID, $score)
    {
        if(!$this->isFaculty())
        {
            http_response_code(\Scholarship\Http\StatusCodes::UNAUTHORIZED);
            die();
        }
        else if($this->checkUserExists($studentID) == false || $this->isValidRatingType($scholarshipID,$ratingTypeID) == false)
        {
            http_response_code(\Scholarship\Http\StatusCodes::BAD_REQUEST);

        }
        else
        {
               if((filter_var($score,FILTER_VALIDATE_INT,array("options"=>array("min_range"=>0,"max_range"=>5)))||$score===0)
                && filter_var($studentID,FILTER_VALIDATE_INT)
                && filter_var($facultyID,FILTER_VALIDATE_INT)
                && filter_var($scholarshipID,FILTER_VALIDATE_INT)
                && filter_var($ratingTypeID,FILTER_VALIDATE_INT))
            {
                $rating = new Rating($studentID, $facultyID, $scholarshipID, $ratingTypeID);
                $rating -> setScore($score);
                $rating -> saveToDB();
                http_response_code(\Scholarship\Http\StatusCodes::CREATED);
            } else {
                http_response_code(\Scholarship\Http\StatusCodes::BAD_REQUEST);
            }
        }
    }

    public function getApplicationScore($studentID, $facultyID, $scholarshipID, $ratingTypeID)
    {

        if(!$this->isFaculty())
        {
            http_response_code(\Scholarship\Http\StatusCodes::UNAUTHORIZED);
            die();

        }
        else if($this->checkUserExists($studentID) == false || $this->isValidRatingType($scholarshipID,$ratingTypeID) == false )
        {
            http_response_code(\Scholarship\Http\StatusCodes::BAD_REQUEST);

        }else{
            $rating = new Rating($studentID, $facultyID, $scholarshipID, $ratingTypeID);
            http_response_code(\Scholarship\Http\StatusCodes::CREATED);
            return $rating;

        }
    }


    //update student past score
    public function updateStudentRating($facultyID,$studentID, $score)
    {
        if($this->checkUserExists($studentID) == false)
        {
            http_response_code(\Scholarship\Http\StatusCodes::BAD_REQUEST);

        }
        else if($this->isFaculty())
        {
            if((filter_var($score,FILTER_VALIDATE_INT,array("options"=>array("min_range"=>0,"max_range"=>5)))||$score===0)
                && filter_var($studentID,FILTER_VALIDATE_INT)
                && filter_var($facultyID,FILTER_VALIDATE_INT)){
                $rating = new Rating($studentID, $facultyID);
                $rating->setScore($score);
                //$rating->SaveToDB();
                http_response_code(\Scholarship\Http\StatusCodes::CREATED);
            } else {
                http_response_code(\Scholarship\Http\StatusCodes::BAD_REQUEST);
            }
        }else{
            http_response_code(\Scholarship\Http\StatusCodes::UNAUTHORIZED);
            die();
        }

    }

    public function checkUserExists($studentID)
    {
        $user = new User();
        $user->setWNumber($studentID);
        return $user->userExists();
    }

    public function getFacultyID()
    {
        $userName=Token::getUsernameFromToken();
        if (Token::getRoleFromToken() == Token::ROLE_FACULTY)
            return User::getIDByUsername($userName);
        else
            return -1;

    }

    private function isFaculty()
    {
        if (Token::getRoleFromToken() == Token::ROLE_FACULTY)
            return true;
        else
            return false;
    }

    public function isValidRatingType($ScholarshipID, $ratingTypeID) : bool
    {

            $dbh = DatabaseConnection::getInstance();
            $stmt = $dbh->prepare("SELECT * FROM `RatingTypes` WHERE ScholarshipID = :ScholarshipID AND RatingTypeID = :ratingTypeID");
            $stmt->bindParam(':ScholarshipID',$ScholarshipID);
        $stmt->bindParam(':ratingTypeID',$ratingTypeID);
            $stmt->setFetchMode(\PDO::FETCH_NUM);
            $success = $stmt->execute();
            if (!$success)
            {
                throw new \PDOException("error: Scholarship does't exist");
            }
            else
            {
                return ($stmt->rowCount() != 0 ? true : false);
            }

    }

    public function checkScholarshipById($ScholarshipID) : bool
    {

        $dbh = DatabaseConnection::getInstance();
        $stmt = $dbh->prepare("SELECT * FROM `RatingTypes` WHERE ScholarshipID = :ScholarshipID ");
        $stmt->bindParam(':ScholarshipID',$ScholarshipID);

        $stmt->setFetchMode(\PDO::FETCH_NUM);
        $success = $stmt->execute();
        if (!$success)
        {
            throw new \PDOException("error: Scholarship does't exist");
        }
        else
        {
            return ($stmt->rowCount() != 0 ? true : false);
        }

    }



}