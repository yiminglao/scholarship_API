<?php
/**
 * Created by PhpStorm.
 * User: Monkey Park
 * Date: 11/28/2017
 * Time: 4:56 PM
 */

namespace Scholarship\Testing;

use Scholarship\Controllers\RatingController;
use Scholarship\Models\Token as token;
use Scholarship\Controllers\TokensController;
use Scholarship\Http\Methods;
use Scholarship\Utilities\Testing;
use PHPUnit\Framework\TestCase;

class RatingControllerTest extends TestCase
{

    private function generateToken($username, $password)
    {
        $tokenController = new TokensController();
        return $tokenController->buildToken($username, $password);
    }

    public function testGetAllRatingByFaculty()
    {
        $token = $this->generateToken('genericfac', 'Hello896');
        $this->assertNotNull($token);
        $this->assertEquals(Token::ROLE_FACULTY, Token::getRoleFromToken($token));

        $body_contents = array("Authorization"=>'Bearer '.$token);
        $body = json_encode($body_contents);
        $endpoint = "/ratings";

        try {
            $output = Testing::callAPIOverHTTP($endpoint, Methods::GET, $body, $token, Testing::JSON);
        } catch (\Exception $err) {
            $this->assertEmpty($err->getMessage(), "Error message: ". $err->getMessage());
        }

        $this->assertNotFalse($output); //False on error, otherwise it's the raw results. You should be able to json_decode to read the response.
        $this->assertEquals(201, Testing::getLastHTTPResponseCode());
    }

    public function testGetAllRatingByStudent()
    {
        $token = $this->generateToken('generic', 'Hello357');
        $this->assertNotNull($token);
        $this->assertEquals(Token::ROLE_STUDENT, Token::getRoleFromToken($token));

        $body_contents = array("Authorization"=>'Bearer '.$token);
        $body = json_encode($body_contents);
        $endpoint = "/ratings";

        try {
            $output = Testing::callAPIOverHTTP($endpoint, Methods::GET, $body, $token, Testing::JSON);
        } catch (\Exception $err) {
            $this->assertEmpty($err->getMessage(), "Error message: ". $err->getMessage());
        }

        $this->assertNotFalse($output); //False on error, otherwise it's the raw results. You should be able to json_decode to read the response.
        $this->assertEquals(401, Testing::getLastHTTPResponseCode());
    }

    public function testGetCompositeRatingByStudent()
    {
        $token = $this->generateToken('generic', 'Hello357');
        $this->assertNotNull($token);
        $this->assertEquals(Token::ROLE_STUDENT, Token::getRoleFromToken($token));

        $body_contents = array("Authorization"=>'Bearer '.$token);
        $body = json_encode($body_contents);
        $scholarshipId = 2;
        $studentId = 3;


        if(RatingController::checkScholarshipById($scholarshipId) == false || RatingController::checkUserExists($studentId) == false)
        {
            $this->assertEquals(400, Testing::getLastHTTPResponseCode());
        }
        else {
            try {
                $endpoint = "/scholarship/$scholarshipId/ratings/$studentId";
                $output = Testing::callAPIOverHTTP($endpoint, Methods::GET, $body, $token, Testing::JSON);
            } catch (\Exception $err) {
                $this->assertEmpty($err->getMessage(), "Error message: " . $err->getMessage());
            }

            $this->assertNotFalse($output); //False on error, otherwise it's the raw results. You should be able to json_decode to read the response.
            $this->assertEquals(401, Testing::getLastHTTPResponseCode());
        }
    }

    public function testGetCompositeRatingByFaculty()
    {
        $token = $this->generateToken('genericfac', 'Hello896');
        $this->assertNotNull($token);
        $this->assertEquals(Token::ROLE_FACULTY, Token::getRoleFromToken($token));

        $body_contents = array("Authorization"=>'Bearer '.$token);
        $body = json_encode($body_contents);
        $scholarshipId = 2;
        $studentId = 100;

        if(RatingController::checkScholarshipById($scholarshipId) == false || RatingController::checkUserExists($studentId) == false)
        {
            //$this->assertNotFalse($output); //False on error, otherwise it's the raw results. You should be able to json_decode to read the response.
            $this->assertEquals(400, Testing::getLastHTTPResponseCode());
        }
        else
        {
            try {
                $endpoint = "/scholarship/$scholarshipId/ratings/$studentId";
                $output = Testing::callAPIOverHTTP($endpoint, Methods::GET, $body, $token, Testing::JSON);
            } catch (\Exception $err) {
                $this->assertEmpty($err->getMessage(), "Error message: " . $err->getMessage());
            }

            $this->assertNotFalse($output); //False on error, otherwise it's the raw results. You should be able to json_decode to read the response.
            $this->assertEquals(200, Testing::getLastHTTPResponseCode());
        }

    }
    public function testGetStudentPastScore()
    {
        $token = $this->generateToken('genericfac', 'Hello896');
        $this->assertNotNull($token);
        $this->assertEquals(Token::ROLE_FACULTY, Token::getRoleFromToken($token));

        $body_contents = array("Authorization"=>'Bearer '.$token);
        $body = json_encode($body_contents);

        $studentId = 100;


        if(RatingController::checkUserExists($studentId) == false)
        {
            $this->assertEquals(400, Testing::getLastHTTPResponseCode());
        }
        else
        {
            try {
                $endpoint = "/ratings/student/$studentId";
                $output = Testing::callAPIOverHTTP($endpoint, Methods::GET, $body, $token, Testing::JSON);
            } catch (\Exception $err) {
                $this->assertEmpty($err->getMessage(), "Error message: " . $err->getMessage());
            }

            $this->assertNotFalse($output); //False on error, otherwise it's the raw results. You should be able to json_decode to read the response.
            $this->assertEquals(200, Testing::getLastHTTPResponseCode());
        }

    }

    public function testUpdateStudentRating()
    {
        $facultyToken = $this->generateToken('genericfac', 'Hello896');
        $studentToken = $this->generateToken('generic', 'Hello357');
        $this->assertEquals(Token::ROLE_FACULTY, Token::getRoleFromToken($facultyToken));
        $this->assertEquals(Token::ROLE_STUDENT, Token::getRoleFromToken($studentToken));

        //Test with no score
        $body_contents = array("Authorization"=>'Bearer '.$facultyToken);
        $body = json_encode($body_contents);
        $endpoint = "/ratings/student/1";
        $this->ApplicationScoreAssertions($endpoint,$body,$facultyToken,400,Methods::PUT);//faculty
        $this->ApplicationScoreAssertions($endpoint,$body,$studentToken,401,Methods::PUT);//faculty

        //test with valid score
        $body_contents = array("Authorization"=>'Bearer '.$facultyToken, "score"=>0);
        $body = json_encode($body_contents);
        $this->ApplicationScoreAssertions($endpoint,$body,$facultyToken,201,Methods::PUT);
        $this->ApplicationScoreAssertions($endpoint,$body,$studentToken,401,Methods::PUT);


        //test with invalid studentID
        $body_contents = array("score"=>0);
        $body = json_encode($body_contents);
        $endpoint = "/ratings/student/1000";
        $this->ApplicationScoreAssertions($endpoint,$body,$facultyToken,400,Methods::PUT);
        $this->ApplicationScoreAssertions($endpoint,$body,$studentToken,400,Methods::PUT);

        //test with score too high or too low
        $body_contents = array("score"=>-1);
        $body = json_encode($body_contents);
        $endpoint = "/ratings/student/1";
        $this->ApplicationScoreAssertions($endpoint,$body,$facultyToken,400,Methods::PUT);
        $this->ApplicationScoreAssertions($endpoint,$body,$studentToken,401,Methods::PUT);
        $body_contents = array("score"=>6);
        $body = json_encode($body_contents);
        $endpoint = "/ratings/student/1";
        $this->ApplicationScoreAssertions($endpoint,$body,$facultyToken,400,Methods::PUT);
        $this->ApplicationScoreAssertions($endpoint,$body,$studentToken,401,Methods::PUT);

    }


    public function testPostStudentScore()
    {
        $facultyToken = $this->generateToken('genericfac', 'Hello896');
        $studentToken = $this->generateToken('generic', 'Hello357');
        $this->assertEquals(Token::ROLE_FACULTY, Token::getRoleFromToken($facultyToken));
        $this->assertEquals(Token::ROLE_STUDENT, Token::getRoleFromToken($studentToken));

        //Test with no score
        $body_contents = array("Authorization"=>'Bearer '.$facultyToken);
        $body = json_encode($body_contents);
        $endpoint = "/ratings/student/1";
        $this->ApplicationScoreAssertions($endpoint,$body,$facultyToken,400,Methods::POST);
        $this->ApplicationScoreAssertions($endpoint,$body,$studentToken,401,Methods::POST);

        //test with valid score
        $body_contents = array("Authorization"=>'Bearer '.$facultyToken, "score"=>0);
        $body = json_encode($body_contents);
        $this->ApplicationScoreAssertions($endpoint,$body,$facultyToken,201,Methods::POST);
        $this->ApplicationScoreAssertions($endpoint,$body,$studentToken,401,Methods::POST);

        //test with invalid studentID
        $body_contents = array("score"=>0);
        $body = json_encode($body_contents);
        $endpoint = "/ratings/student/700";
        $this->ApplicationScoreAssertions($endpoint,$body,$facultyToken,400,Methods::POST);
        $this->ApplicationScoreAssertions($endpoint,$body,$studentToken,400,Methods::POST);

        //test with score too high or too low
        $body_contents = array("score"=>-1);
        $body = json_encode($body_contents);
        $endpoint = "/ratings/student/1";
        $this->ApplicationScoreAssertions($endpoint,$body,$facultyToken,400,Methods::POST);
        $this->ApplicationScoreAssertions($endpoint,$body,$studentToken,401,Methods::POST);
        $body_contents = array("score"=>6);
        $body = json_encode($body_contents);
        $endpoint = "/ratings/student/1";
        $this->ApplicationScoreAssertions($endpoint,$body,$facultyToken,400,Methods::POST);
        $this->ApplicationScoreAssertions($endpoint,$body,$studentToken,401,Methods::POST);

    }


    public function testPostApplicationScore()
    {
        $facultyToken = $this->generateToken('genericfac', 'Hello896');
        $studentToken = $this->generateToken('generic', 'Hello357');
        $this->assertEquals(Token::ROLE_FACULTY, Token::getRoleFromToken($facultyToken));
        $this->assertEquals(Token::ROLE_STUDENT, Token::getRoleFromToken($studentToken));

        //Test with no score or Rating Type
        $body_contents = array("Authorization"=>'Bearer '.$facultyToken);
        $body = json_encode($body_contents);
        $endpoint = "/ratings/student/1/scholarship/1";
        $this->ApplicationScoreAssertions($endpoint,$body,$facultyToken,400,Methods::POST);//faculty
        $this->ApplicationScoreAssertions($endpoint,$body,$studentToken,401,Methods::POST);//faculty

        //test with valid score and ratingtypeid
        $body_contents = array("Authorization"=>'Bearer '.$facultyToken, "score"=>0,"ratingTypeId"=>1);
        $body = json_encode($body_contents);
        $this->ApplicationScoreAssertions($endpoint,$body,$facultyToken,201,Methods::POST);
        $this->ApplicationScoreAssertions($endpoint,$body,$studentToken,401,Methods::POST);


        //test with valid score and invalid ratingtypeid
        $body_contents = array("score"=>0,"ratingTypeId"=>22);
        $body = json_encode($body_contents);
        $this->ApplicationScoreAssertions($endpoint,$body,$facultyToken,400,Methods::POST);
        $this->ApplicationScoreAssertions($endpoint,$body,$studentToken,401,Methods::POST);
        $body_contents = array("score"=>0,"ratingTypeId"=>"AppleBeatsAndroid");
        $body = json_encode($body_contents);
        $this->ApplicationScoreAssertions($endpoint,$body,$facultyToken,400,Methods::POST);
        $this->ApplicationScoreAssertions($endpoint,$body,$studentToken,401,Methods::POST);

        //test with invalid studentID, valid score and ratingtypeid
        $body_contents = array("score"=>0,"ratingTypeId"=>1);
        $body = json_encode($body_contents);
        $endpoint = "/ratings/student/1000/scholarship/1";
        $this->ApplicationScoreAssertions($endpoint,$body,$facultyToken,400,Methods::POST);
        $this->ApplicationScoreAssertions($endpoint,$body,$studentToken,401,Methods::POST);

        //test with score too high or too low
        $body_contents = array("score"=>-1,"ratingTypeId"=>1);
        $body = json_encode($body_contents);
        $endpoint = "/ratings/student/1/scholarship/1";
        $this->ApplicationScoreAssertions($endpoint,$body,$facultyToken,400,Methods::POST);
        $this->ApplicationScoreAssertions($endpoint,$body,$studentToken,401,Methods::POST);
        $body_contents = array("score"=>6,"ratingTypeId"=>1);
        $body = json_encode($body_contents);
        $endpoint = "/ratings/student/1/scholarship/1";
        $this->ApplicationScoreAssertions($endpoint,$body,$facultyToken,400,Methods::POST);
        $this->ApplicationScoreAssertions($endpoint,$body,$studentToken,401,Methods::POST);
    }

    public function ApplicationScoreAssertions($endpoint,$body,$token,$code,$method)
    {
        try {
            $output = Testing::callAPIOverHTTP($endpoint, $method, $body, $token, Testing::JSON);
        } catch (\Exception $err) {
            $this->assertEmpty($err->getMessage(), "Error message: ". $err->getMessage());
        }

        $this->assertNotFalse($output); //False on error, otherwise it's the raw results. You should be able to json_decode to read the response.
        $this->assertEquals($code, Testing::getLastHTTPResponseCode());
    }



}
