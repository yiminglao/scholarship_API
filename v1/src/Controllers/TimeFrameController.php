<?php
/**
 * Created by PhpStorm.
 * User: Nick
 * Date: 11/2/2017
 * Time: 12:50 PM
 */

namespace Scholarship\Controllers;

use \Scholarship\Models\TimeFrame as TimeFrame;
use \Scholarship\Http\StatusCodes as StatusCodes;
use \Scholarship\Models\Token as Token;

class TimeFrameController
{
    private $timeFrame;

    public function __construct()
    {
        $this->timeFrame = new TimeFrame();
    }

    //Receive json object and decide which function to call
    function updateDateObject($json) {

        if (Token::getRoleFromToken() == Token::ROLE_FACULTY) {
            if (array_key_exists("endDate", $json) && array_key_exists("startDate", $json) && array_key_exists("id",$json)) {
                $jsonObj = $json;

                if ($jsonObj->id != NULL && $jsonObj->startDate != NULL && $jsonObj->endDate != NULL) {
                    $this->timeFrame->updateDate($jsonObj->id, $jsonObj->startDate, $jsonObj->endDate);
                } else if ($jsonObj->id != NULL && $jsonObj->startDate != NULL && $jsonObj->endDate == NULL) {
                    $this->timeFrame->updateStartDate($jsonObj->id, $jsonObj->startDate);
                } else if ($jsonObj->id != NULL && $jsonObj->startDate == NULL && $jsonObj->endDate != NULL) {
                    $this->timeFrame->updateEndDate($jsonObj->id, $jsonObj->endDate);
                } else {
                    http_response_code(StatusCodes::BAD_REQUEST);
                    exit("Request was incorrect or uninterpretable");
                }
            }
            else {
                http_response_code(StatusCodes::BAD_REQUEST);
            }
        }
        else
        {
            http_response_code(StatusCodes::UNAUTHORIZED);
        }
    }

    function createDateObject($json)
    {
        if (Token::getRoleFromToken() == Token::ROLE_FACULTY) {
            if (array_key_exists("endDate", $json) && array_key_exists("startDate", $json) && array_key_exists("id",$json)) {
                if ($json->id == NULL && $json->startDate != NULL && $json->endDate != NULL) {
                    $this->timeFrame->createTimeFrame($json->startDate, $json->endDate);
                } else {
                    http_response_code(StatusCodes::BAD_REQUEST);
                    exit("Request was incorrect or uninterpretable");
                }
            } else {
                http_response_code(StatusCodes::BAD_REQUEST);
            }
        }
        else {
            http_response_code(StatusCodes::UNAUTHORIZED);
        }
    }

    function deleteTimeframeObject($json)
    {
        if (Token::getRoleFromToken() == Token::ROLE_FACULTY)
        {
            if(array_key_exists("id", $json)) {
                if ($json->id != NULL) {
                    $this->timeFrame->deleteTimeFrame($json->id);
                } else {
                    http_response_code(StatusCodes::BAD_REQUEST);
                    exit("Request was incorrect or uninterpretable");
                }
            }
            else{
                http_response_code(StatusCodes::BAD_REQUEST);
            }
        }
        else
        {
            http_response_code(StatusCodes::UNAUTHORIZED);
        }
    }

    function getAllDateObjects($id) {

        return $this->timeFrame->getAllDates();
    }

    function getStartDateObject($id) {
        $timeObject = $this->timeFrame->getDate(intval($id));
        return $timeObject[0]['startDate'];
    }

    function getEndDateObject($id) {
        $timeObject = $this->timeFrame->getDate(intval($id));
        return $timeObject[0]['endDate'];
    }

    function getDateObject($id) {
            $timeObject = $this->timeFrame->getDate(intval($id));
            return $timeObject;
    }

    function getWithinDateObject($id) {
        $return = $this->timeFrame->isWithinTimeFrame(intval($id));
        return $return;
    }
}