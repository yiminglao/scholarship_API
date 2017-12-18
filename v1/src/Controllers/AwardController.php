<?php
/**
 * Created by PhpStorm.
 * User: Dan
 * Date: 11/1/17
 * Time: 8:25 AM
 */

namespace Scholarship\Controllers;

use \Scholarship\Http\StatusCodes as StatusCodes;
use \Scholarship\Models\Award as Award;
use Scholarship\Models\Token as Token;
use Scholarship\Utilities\DatabaseConnection;

define('__ROOT__', dirname(dirname(__FILE__)));
require_once(__ROOT__.'/Utilities/DatabaseConnection.php');

class AwardController
{

    public function getAllAwards() {
        $role = Token::getRoleFromToken();
        if($role == Token::ROLE_FACULTY) {
            return Award::getAllAwards();
        }
        else {
            http_response_code(StatusCodes::UNAUTHORIZED);
            return "Not a Faculty.";
        }
    }

    public function getAwardByUserID($args){
        $role = Token::getRoleFromToken();
        if($role == Token::ROLE_FACULTY){

            return Award::getAllAwardsByUserID($args['id']);
        }
        else {
            http_response_code(StatusCodes::UNAUTHORIZED);
            return "Not a Faculty.";
        }
    }

    public function getAwardByScholarshipID($args){
        $role = Token::getRoleFromToken();
        if($role == Token::ROLE_FACULTY) {
            return Award::getAwardByScholarshipID($args['id']);
        }
        else {
            http_response_code(StatusCodes::UNAUTHORIZED);
            return "Not a Faculty.";
        }

    }

    //Below is the code for POST of an AWARD
    public function buildAward(int $scholarshipId, int $userId, int $timeframeId, int $awardAmount, string $decisionDate, string $decision, string $authorization){
        $buildAward = new Award();
        Token::getRoleFromToken($authorization);
       $buildAward->init($scholarshipId,$userId,$timeframeId,$awardAmount,$decisionDate,$decision);

       return  'A new Award has been created!';

    }

    public function updateAward(int $id, int $scholarshipId, int $userId, int $timeframeId, int $awardAmount, string $decisionDate, string $decision, string $authorization){
        $updateAward = new Award();
        Token::getRoleFromToken($authorization);
        $updateAward->updateAward($id,$scholarshipId,$userId,$timeframeId,$awardAmount,$decisionDate,$decision);
        return  'Award has been updated!';
    }


    //d
    public function setAwardDecision($id, $decision){
        $role = Token::getUsernameFromToken();
        if($role == Token::ROLE_STUDENT) {
            $award = Award::getAwardByAwardId($id);
            if($award->userId != Token::getUsernameFromToken()){
                http_response_code(403);
                return;
            }
            $award->setDecision($decision);
            $award->update();
            http_response_code(200);
            return $award;
        }
        $award = new Award();
        $award->setAwardDecision($id, $decision);
        return 'Award Decision has been set';
    }
    //d
    public function getAllOfAStudentsAwards(int $userId){
        return Award::getAllAwardsByUserID($userId);
    }
    //d
    public function getSpecificAward(int $id){
        return Award::getAwardByAwardId($id);
    }


}