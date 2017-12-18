<?php
/**
 * Created by PhpStorm.
 * User: CW
 * Date: 10/30/2017
 * Time: 8:54 AM
 */

namespace Scholarship\Controllers;


use Scholarship\Models\Application;
use Scholarship\Models\Response;
use Scholarship\Models\Token as Token;
use Scholarship\Http\StatusCodes as StatusCodes;
use Scholarship\Utilities\DatabaseConnection as DatabaseConnection;

class ApplicationsController
{
    //The database handler
    private $dbh;
    private $applicationID;
    private $responseID;

    public function __construct()
    {
        $this->dbh = DatabaseConnection::getInstance();
    }

    public function POST()
    {
        $role = Token::getRoleFromToken();

        if($role == Token::ROLE_STUDENT) {

            $json = (object) json_decode(file_get_contents('php://input'));
            $application = new Application();
            $responses = array();

            if(isset($json->scholarshipID)) {
                //Set given variables for application
                $application->setUserID(str_replace('W', '', Token::getUsernameFromToken()));
                $application->setScholarshipID(filter_var($json->scholarshipID, FILTER_SANITIZE_STRING));

                // Test to see if the user already has an application for this scholarship.
                // If there is one, it is a bad request and should be done via PUT to edit.
                // (Otherwise, it will create another application for the user for the same scholarship...)
                $application->load();
                if ($application->getApplicationID()) {
                    http_response_code(StatusCodes::BAD_REQUEST);
                    return null;
                }

                //Saving the application up here sets its applicationID
                $application->saveApplication();

                //Create Response objects for each response
                foreach ($json->responses as $jsonResponse)
                {
                    if(isset($jsonResponse->questionID) && isset($jsonResponse->responseText)) {
                        $response = new Response();
                        $response->setQuestionID(filter_var($jsonResponse->questionID, FILTER_SANITIZE_STRING));
                        $response->setResponseText(filter_var($jsonResponse->responseText, FILTER_SANITIZE_STRING));
                        $response->setApplicationID($application->getApplicationID());
                        $response->saveResponse();

                        $responses[] = $response;
                    }
                    else {
                        http_response_code(StatusCodes::BAD_REQUEST);
                        return null;
                    }
                }
            }
            else {
                http_response_code(StatusCodes::BAD_REQUEST);
                return null;
            }
        }
        else if($role == Token::ROLE_FACULTY) {
            http_response_code(StatusCodes::FORBIDDEN);
            return null;
        }
    }

    public function PUT()
    {
        $role = Token::getRoleFromToken();

        if($role == Token::ROLE_STUDENT) {

            $json = (object) json_decode(file_get_contents('php://input'));
            $responses = array();

            //Create Response objects for each response
            foreach ($json->responses as $jsonResponse)
            {
                if(isset($jsonResponse->responseID) && isset($jsonResponse->questionID) && isset($jsonResponse->responseText)) {
                    $response = new Response();
                    $response->setResponseID(filter_var($jsonResponse->responseID, FILTER_SANITIZE_STRING));
                    $response->setQuestionID(filter_var($jsonResponse->questionID, FILTER_SANITIZE_STRING));
                    $response->setResponseText(filter_var($jsonResponse->responseText, FILTER_SANITIZE_STRING));
                    $response->saveResponse(str_replace('W', '', Token::getUsernameFromToken()));

                    $responses[] = $response;
                }
                else {
                    //This is a bad request, so set the response code and return nothing
                    http_response_code(StatusCodes::BAD_REQUEST);
                    return null;
                }
            }

        }
        else if($role == Token::ROLE_FACULTY) {
            http_response_code(StatusCodes::FORBIDDEN);
            //What to do on unknown user?
            return null;
        }
    }
    /*
     * @Param $userID: This is the userID of a student.
     * This method gets all of the applications for a specific student, or all of the applications if you are a faculty member.
     */
    public function GET($applicationID) {
        $role = Token::getRoleFromToken();

        //Check the input data
        if(count($applicationID) > 1) {
            //This is a bad request, so set the response code and return nothing
            http_response_code(StatusCodes::BAD_REQUEST);
            return null;
        }
        else {
            //Get the ID value from the array
            //There is only 1 item in the array, so this is okay.
            //I can't figure out the correct index, so this will have to do for now.
            foreach ($applicationID as $id) {
                $applicationID = $id;
            }
        }

        if($role == Token::ROLE_STUDENT) {
            //Students can only get their own applications
            if($applicationID == null) {
                //Get all of the application information from the database for this user
                $args = array('userID' => str_replace('W', '', Token::getUsernameFromToken()));
                return Application::loadApplications($args);
            }
            else {
                $args = array('userID' => str_replace('W', '', Token::getUsernameFromToken()), 'applicationID' => $applicationID);
                $retVal = Application::loadApplications($args);
                if(count($retVal) == 1) {
                    return $retVal[0];
                }
                else {
                    //You do not have access to this application
                    http_response_code(StatusCodes::FORBIDDEN);
                    return null;
                }
            }
        }
        else if($role == Token::ROLE_FACULTY) {
            //Faculty can get every application
            if($applicationID == null) {
                $args = array();
                return Application::loadApplications($args);
            }
            else {
                $args = array('applicationID' => $applicationID);
                $retVal = Application::loadApplications($args);
                if(count($retVal) == 1) {
                    return $retVal[0];
                }
                else {
                    //Faculty have access to all applications, so it wasn't found.
                    http_response_code(StatusCodes::NOT_FOUND);
                    return null;
                }
            }
        }
        else {
            http_response_code(StatusCodes::UNAUTHORIZED);
            //What to do on unknown user?
            return null;
        }
    }

    public function getApplication($applicationID)
    {
        return $this->GET($applicationID);
    }

    public function getResponses($applicationID)
    {
        return $this->GET($applicationID)->getResponses();
    }
}