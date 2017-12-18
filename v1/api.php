<?php
/**
 * Created by PhpStorm.
 * User: iamcaptaincode
 * Date: 10/13/2016
 * Time: 8:56 AM
 */

require_once 'config.php';
require_once 'vendor/autoload.php';
use Scholarship\Http\Methods as Methods;
use Scholarship\Controllers\TimeFrameController as TimeFrameController;
use Scholarship\Controllers\ScholarshipController as ScholarshipController;

$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r)  use ($baseURI) {
    /** TOKENS CLOSURES */
    $handlePostToken = function ($args) {
        $tokenController = new \Scholarship\Controllers\TokensController();
        //Is the data via a form?
        if (!empty($_POST['username'])) {
            $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
            $password = $_POST['password'] ?? "";
        } else {
            //Attempt to parse json input
            $json = (object) json_decode(file_get_contents('php://input'));
            if (count((array)$json) >= 2) {
                $username = filter_var($json->username, FILTER_SANITIZE_STRING);
                $password = $json->password;
            } else {
                http_response_code(\Scholarship\Http\StatusCodes::BAD_REQUEST);
                exit();
            }
        }
        return $tokenController->buildToken($username, $password);

    };

    /*TEST CLOSURE*/
    $testScholarshipModel = function ($args) {
        $scholarship = new ScholarshipController();

        $val = $scholarship->test(3);

        return $val;
    };


    $getAllScholarships = function ($args)
    {
        $scholarship = new ScholarshipController();

        $val = $scholarship->getAllScholarships($args);

        return $val;


    };

    /**Routes **/
    /** SCHOLARSHIP CLOSURE **/
    $handleScholarship = function ($args) {
        $scholarshipController = new ScholarshipController();
        return $scholarshipController->getScholarshipById($args['id']);
    };


    $updateScholarship = function ($args) {
        $scholarshipCtrl = new ScholarshipController();
    $json = (object) json_decode(file_get_contents('php://input'));

        return $scholarshipCtrl->update($args['id'], $json);
    };

    $createScholarship = function ($args) {
        $scholarshipCtrl = new ScholarshipController();
        $json = $_POST;
        if (empty($json)) {
            $json = (object) json_decode(file_get_contents('php://input'));
        }

        return $scholarshipCtrl->create($json);
    };

    $deleteScholarship = function ($args) {
        $scholarshipCtrl = new ScholarshipController();
        return $scholarshipCtrl->delete($args['id']);
    };

    /*** AWARD CLOSURES */
    $handelGetAward = function ($args) {
        $awardController = new \Scholarship\Controllers\AwardController();
        $awards = $awardController->getAllAwards($args);
        return $awards;
    };

    $handelGetAwardByUserID = function ($args) {
        $awardController = new \Scholarship\Controllers\AwardController();
        $awards = $awardController->getAwardByUserID($args);
        return $awards;
    };

    $handelGetAwardByScholarshipID = function ($args) {
        $awardController = new \Scholarship\Controllers\AwardController();
        $awards = $awardController->getAwardByScholarshipID($args);
        return $awards;

    };

    $handlePostAward = function ($args) {
        $json = json_decode(file_get_contents('php://input'));
        $awardController = new \Scholarship\Controllers\AwardController();
        $scholarshipId = $json->scholarshipId;
        $userId = $json->userId;
        $timeframeId = $json->timeframeId;
        $awardAmount = $json->awardAmount;
        $decisionDate = $json->decisionDate;
        $decision = $json->decision;
        $headers = apache_request_headers();
        $authorization = $headers['Authorization'];

        return $awardController->buildAward($scholarshipId, $userId, $timeframeId, $awardAmount,  $decisionDate,  $decision, $authorization);

    };

    $handlePutAward = function ($args) {
        $json = json_decode(file_get_contents('php://input'));
        $awardController = new \Scholarship\Controllers\AwardController();
        $id = $json->id;
        $scholarshipId = $json->scholarshipId;
        $userId = $json->userId;
        $timeframeId = $json->timeframeId;
        $awardAmount = $json->awardAmount;
        $decisionDate = $json->decisionDate;
        $decision = $json->decision;
        $headers = apache_request_headers();
        $authorization = $headers['Authorization'];

        return $awardController->updateAward($id,$scholarshipId, $userId, $timeframeId, $awardAmount,  $decisionDate,  $decision, $authorization);
    };

    $handleGetAllStudentAwards = function ($args){
        $awardController = new Scholarship\Controllers\AwardController();
        return $awardController->getAllOfAStudentsAwards($args['userId']);
    };

    $handleGetStudentAward = function ($args){
        $awardController = new Scholarship\Controllers\AwardController();
        return $awardController->getSpecificAward($args['id']);
    };

    $handleUpdateDecision = function ($args){
        $awardController = new Scholarship\Controllers\AwardController();
        $json = json_decode(file_get_contents('php://input'));
        $id = $args['id'];
        $decision = $json->decision;
        return $awardController->setAwardDecision($id, $decision);
    };

    $handleGetApplication = function($args) {
        $applicationController = new \Scholarship\Controllers\ApplicationsController() ;
        return $applicationController->GET($args);
    };

    $handlePostApplication = function($args) {
        $applicationController = new \Scholarship\Controllers\ApplicationsController() ;
        return $applicationController->Post($args);
    };

    $handlePutApplication = function($args) {
        $applicationController = new \Scholarship\Controllers\ApplicationsController() ;
        return $applicationController->Put($args);
    };

    /** TIMEFRAME CLOSURES */
    $handlePostTimeFrame = function ($args){
        $timeFrameController = new \Scholarship\Controllers\TimeFrameController();

        //Attempt to parse json input
        $timeFrameController = new \Scholarship\Controllers\TimeFrameController();

        //Attempt to parse json input
        $json = (object) json_decode(file_get_contents('php://input') , true);
        if (is_null($json)) {
            http_response_code(\Scholarship\Http\StatusCodes::BAD_REQUEST);
            exit();
        }

        return $timeFrameController->createDateObject($json);
    };
    $handleDeleteTimeFrame = function ($args)
    {
        //Attempt to parse json input
        $timeFrameController = new TimeFrameController();

        //Attempt to parse json input
        $json = (object) json_decode(file_get_contents('php://input'), true);
        if (is_null($json)) {
            http_response_code(\Scholarship\Http\StatusCodes::BAD_REQUEST);
            exit();
        }

        return $timeFrameController->deleteTimeframeObject($json);
    };
    $handlePutTimeFrame = function ($args)
    {
        $timeFrameController = new \Scholarship\Controllers\TimeFrameController();

        //Attempt to parse json input
        $json = (object) json_decode(file_get_contents('php://input'), true);
        if (is_null($json)) {
            http_response_code(\Scholarship\Http\StatusCodes::BAD_REQUEST);
            exit();
        }

        return $timeFrameController->updateDateObject($json);
    };
    $handleGetTimeFrame = function($args)
    {
        $timeFrameController = new \Scholarship\Controllers\TimeFrameController();

        //Attempt to parse json input
        $json = (object) json_decode(file_get_contents('php://input'), true);
        if (is_null($json)) {
            http_response_code(\Scholarship\Http\StatusCodes::BAD_REQUEST);
            exit();
        }

        return $timeFrameController->getAllDateObjects($json);
    };
    $handleGetStartDate = function($args)
    {
        $timeFrameController = new \Scholarship\Controllers\TimeFrameController();
        if(isset($args['id']))
        {
            return $timeFrameController->getStartDateObject($args['id']);
        }
    };
    $handleGetEndDate = function($args)
    {
        $timeFrameController = new \Scholarship\Controllers\TimeFrameController();
        if(isset($args['id']))
        {
            return $timeFrameController->getEndDateObject($args['id']);
        }
    };
    $handleGetFullDate = function($args)
    {
        $timeFrameController = new \Scholarship\Controllers\TimeFrameController();
        if(isset($args['id']))
        {
            return $timeFrameController->getDateObject($args['id']);
        }
    };
    $handleGetWithinDate = function($args)
    {
        $timeFrameController = new \Scholarship\Controllers\TimeFrameController();
        if(isset($args['id']))
        {
            return $timeFrameController->getWithinDateObject($args['id']);
        }
    };
    /** TOKEN ROUTE */
    $r->addRoute(Methods::POST, $baseURI . '/tokens', $handlePostToken);

    /** RATINGS CLOSURES */
    // route for scholarship-rest-f17/v1/ratings
    $handleGetAllRating = function ($args){

        return (new Scholarship\Controllers\RatingController)->getAllRating();
    };
    $r->addRoute(Methods::GET,$baseURI.'/ratings',$handleGetAllRating);

    // route for scholarship-rest-f17/v1/scholarship/scholarshipID/ratings/StudentID
    $handleGetSingleRating = function ($args){
        return (new Scholarship\Controllers\RatingController)->getCompositeRating($args['id'],$args['sid']);
    };
    $r->addRoute(Methods::GET,$baseURI.'/scholarship/{sid:\d+}/ratings/{id:\d+}',$handleGetSingleRating);

    // route for scholarship-rest-f17/v1/ratings/faculty/FacultyID/student/StudentID
    //this route to get student past score
    $handleGetStudentRating = function ($args){
        $fid = Scholarship\Controllers\RatingController::getFacultyID();
        return (new Scholarship\Controllers\RatingController)->getStudentPastScore($args['sid'],$fid);
    };

    $r->addRoute(Methods::GET,$baseURI.'/ratings/student/{sid:\d+}',$handleGetStudentRating);

    $handleUpdateStudentRating = function ($args){
        if (!empty($_POST['score'])) {
            $score = filter_var($_POST['score'], FILTER_SANITIZE_STRING);
        } else {

            $json = (object) json_decode(file_get_contents('php://input'),true);

            if(isset($json->score)){

                $score = $json->score;
            } else {

                $score = -1;
            }

        }
        $fid = Scholarship\Controllers\RatingController::getFacultyID();
        return (new Scholarship\Controllers\RatingController)->updateStudentRating($fid,$args['sid'],$score);
    };

    $r->addRoute(Methods::PUT,$baseURI.'/ratings/student/{sid:\d+}',$handleUpdateStudentRating);

    $r->addRoute(Methods::POST,$baseURI.'/ratings/student/{sid:\d+}',$handleUpdateStudentRating);

//    $r->addRoute(Methods::PUT,$baseURI.'/ratings/faculty/{fid:\d+}/student/{sid:\d+}',$handleUpdateStudentRating);
//
//    $r->addRoute(Methods::POST,$baseURI.'/ratings/faculty/{fid:\d+}/student/{sid:\d+}',$handleUpdateStudentRating);



    //application route
     $handleGetApplicationRating = function ($args){
         //return (new Scholarship\Controllers\RatingController)->getApplicationScore($args['sid'], $args['fid'],$args['ssid'],$args['ratingid']);
         $fid = Scholarship\Controllers\RatingController::getFacultyID();
        return (new Scholarship\Controllers\RatingController)->getApplicationScore($args['sid'], $fid,$args['ssid'],$args['ratingid']);
    };
    //$r->addRoute(Methods::GET,$baseURI.'/ratings/faculty/{fid:\d+}/student/{sid:\d+}/scholarship/{ssid:\d+}/type/{ratingid:\d+}',$handleGetApplicationRating);
    $r->addRoute(Methods::GET,$baseURI.'/ratings/student/{sid:\d+}/scholarship/{ssid:\d+}/type/{ratingid:\d+}',$handleGetApplicationRating);



    $handleUpdateApplicationRating = function ($args) {
        if (!empty($_POST['score']) && !empty($_POST['ratingTypeId'])) {
            $score = filter_var($_POST['score'], FILTER_SANITIZE_NUMBER_INT);
            $ratingType = filter_var($_POST['ratingTypeId'], FILTER_SANITIZE_NUMBER_INT);

        } else {

            $json = (object) json_decode(file_get_contents('php://input'));
            if(isset($json->score)){

                $score = $json->score;
            } else {

                $score = -1;
            }
            if(isset($json->ratingTypeId)){

                $ratingType = $json->ratingTypeId;
            } else {

                $ratingType = -1;
            }


        }
        $fid = Scholarship\Controllers\RatingController::getFacultyID();
        return (new Scholarship\Controllers\RatingController)->setApplicationScore($args['sid'], $fid,$args['ssid'],$ratingType,$score);

    };
//    $r->addRoute(Methods::PUT, $baseURI.'/ratings/faculty/{fid:\d+}/student/{sid:\d+}/scholarship/{ssid:\d+}',$handleUpdateApplicationRating);
//    $r->addRoute(Methods::POST, $baseURI.'/ratings/faculty/{fid:\d+}/student/{sid:\d+}/scholarship/{ssid:\d+}',$handleUpdateApplicationRating);
    $r->addRoute(Methods::PUT, $baseURI.'/ratings/student/{sid:\d+}/scholarship/{ssid:\d+}',$handleUpdateApplicationRating);
    $r->addRoute(Methods::POST, $baseURI.'/ratings/student/{sid:\d+}/scholarship/{ssid:\d+}',$handleUpdateApplicationRating);






    /** USER CLOSURES */
    $handleFullUpdateUser = function ($args) {
        return (new \Scholarship\Controllers\UserController)->fullUpdateUser($args);
    };
    $handleDeleteUser = function ($args) {
        return (new \Scholarship\Controllers\UserController)->deleteUser($args);
    };

    $handlePartialUpdateUser = function($args) {
        return (new \Scholarship\Controllers\UserController)->updateUser($args);
    };

    $handleGetAllStudents = function(){
        return (new \Scholarship\Controllers\UserController)->getAllStudents();
    };

    $handleGetAllFaculty = function() {
        return (new \Scholarship\Controllers\UserController)->getAllFaculty();
    };

    $handleGetUser = function($args){
      return (new Scholarship\Controllers\UserController)->getUser($args);
    };

    $handleAddUser = function(){
        return (new Scholarship\Controllers\UserController)->addUser();
    };

    /** USER ROUTE */
    $r->addRoute(Methods::PUT, $baseURI.'/users/{id:\d+}', $handleFullUpdateUser);
    $r->addRoute(Methods::PATCH, $baseURI.'/users/{id:\d+}', $handlePartialUpdateUser);
    $r->addRoute(Methods::GET, $baseURI.'/users/students', $handleGetAllStudents);
    $r->addRoute(Methods::GET, $baseURI.'/users/faculties', $handleGetAllFaculty);
    $r->addRoute(Methods::GET,$baseURI.'/users/{id:\d+}', $handleGetUser);
    $r->addRoute(Methods::POST,$baseURI.'/users/', $handleAddUser);
    $r->addRoute(Methods::DELETE, $baseURI.'/users/{id:\d+}', $handleDeleteUser);

    /** SCHOLARSHIP ROUTE */
    $r->addRoute(Methods::GET, $baseURI . '/scholarships/{id:\d+}', $handleScholarship);
    $r->addRoute(Methods::GET, $baseURI. '/scholarships', $getAllScholarships);

    $r->addRoute(Methods::POST, $baseURI . '/scholarships', $createScholarship);
    $r->addRoute(Methods::PATCH, $baseURI . '/scholarships/{id:\d+}', $updateScholarship);
    $r->addRoute(Methods::DELETE, $baseURI . '/scholarships/{id:\d+}', $deleteScholarship);

    $r->addRoute(Methods::POST, $baseURI . '/awards', $handlePostAward);
    $r->addRoute(Methods::PUT, $baseURI . '/awards', $handlePutAward);
    /** AWARD ROUTE */
    $r->addRoute(Methods::GET, $baseURI . '/awards', $handelGetAward);
    $r->addRoute(Methods::GET, $baseURI . '/awards/student/{id:\d+}/faculty', $handelGetAwardByUserID);
    $r->addRoute(Methods::GET, $baseURI . '/awards/scholarship/{id:\d+}', $handelGetAwardByScholarshipID);

    $r->addRoute(Methods::GET, $baseURI . '/awards/student/{userId:\d+}/', $handleGetAllStudentAwards);
    $r->addRoute(Methods::GET, $baseURI . '/awards/{id:\d+}/', $handleGetStudentAward);
    $r->addRoute(Methods::PUT, $baseURI . '/awards/{id:\d+}/decision/',$handleUpdateDecision);

    /** TIMEFRAME ROUTES */
    $r->addRoute(Methods::POST, $baseURI . '/timeframe', $handlePostTimeFrame);
    $r->addRoute( Methods::DELETE, $baseURI . '/timeframe', $handleDeleteTimeFrame);
    $r->addRoute(Methods::PUT, $baseURI . '/timeframe', $handlePutTimeFrame);
    $r->addRoute(Methods::GET, $baseURI . '/timeframe', $handleGetTimeFrame);
    $r->addRoute(Methods::GET, $baseURI . '/timeframe/{id:\d+}', $handleGetFullDate);
    $r->addRoute(Methods::GET, $baseURI . '/timeframe/{id:\d+}/start', $handleGetStartDate);
    $r->addRoute(Methods::GET, $baseURI . '/timeframe/{id:\d+}/end', $handleGetEndDate);
    $r->addRoute(Methods::GET, $baseURI . '/timeframe/{id:\d+}/check', $handleGetWithinDate);

    $r->addRoute(Methods::GET, $baseURI . '/applications/{id:\d+}', $handleGetApplication);
    $r->addRoute(Methods::GET, $baseURI . '/applications', $handleGetApplication);
    $r->addRoute(Methods::POST, $baseURI . '/applications', $handlePostApplication);
    $r->addRoute(Methods::PUT, $baseURI . '/applications', $handlePutApplication);
});

$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

$pos = strpos($uri, '?');
if ($pos !== false) {
    $uri = substr($uri, 0, $pos);
}
$uri = rtrim($uri, "/");

$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($method, $uri);

switch($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        http_response_code(Scholarship\Http\StatusCodes::NOT_FOUND);
        //Handle 404
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(Scholarship\Http\StatusCodes::METHOD_NOT_ALLOWED);
        //Handle 403
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler  = $routeInfo[1];
        $vars = $routeInfo[2];

        $response = $handler($vars);
        echo json_encode($response);
        break;
}











