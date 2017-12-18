<?php

/**
 * Created by PhpStorm.
 * User: iamcaptaincode
 * Date: 10/18/2016
 * Time: 8:55 AM
 */

namespace Scholarship\Controllers;
use Scholarship\Http\StatusCodes;
use Scholarship\Models\Token;
use Scholarship\Utilities\DatabaseConnection;
use Scholarship\Models\Student;
use Scholarship\Models\User;
use http\Env\Request;


class UserController
{
    /*
     *  @Route("/users/{id}")
     *  @Method("GET")
     */
    function getUser($args)
    {
        $user = new User();
        $user->setWNumber($args['id']);

        if ($user->userExists() === true) {
            $user->load();

            // When the person who sent the request is a faculty
            if (Token::getRoleFromToken() == Token::ROLE_FACULTY) {
                // When the user info that the faculty is retrieving is a student type
                // - faculty can see any student's information
                if ($user->getRole() == User::ROLE_STUDENT) {
                    $user = new Student();
                    $user->setWNumber($args['id']);
                    $user->load();
                    return $user;
                } else {
                    // Check if the user that the person who sent the request is the same person
                    if ($user->getUsername() == Token::getUsernameFromToken()) {
                        return $user;
                    } else {
                        http_response_code(StatusCodes::UNAUTHORIZED);
                        die("Error: you are not authorized to access this information.");
                    }
                }
            } // When the person who sent the request is a student
            elseif (Token::getRoleFromToken() == Token::ROLE_STUDENT) {
                if ($user->getRole() == User::ROLE_STUDENT) {
                    if ($user->getUsername() == Token::getUsernameFromToken()) {
                        $user = new Student();
                        $user->setWNumber($args['id']);
                        $user->load();
                        return $user;
                    } else {
                        http_response_code(StatusCodes::UNAUTHORIZED);
                        die("Error: you are not authorized to access this information.");
                    }
                } else {
                    http_response_code(StatusCodes::UNAUTHORIZED);
                    die("Error: you are not authorized to access this information.");
                }
            }
        }
        return null;
    }

    /*
     *  @Route("/users/")
     *  @Method("POST")
     */
    function addUser()
    {
        $data = (object) json_decode(file_get_contents('php://input'), true);
        //create user or student object
        $user = new User();
        $rl = Token::getRoleFromToken();
        $user->setRole($rl);
        //if role is student create new student otherwise just keep it a user


        // Figure out who sent the request
        $un = Token::getUsernameFromToken();
        $user->setUsername($un);
        if ($rl == User::ROLE_STUDENT)
        {
            $user = new Student();
            $un = Token::getUsernameFromToken();
            $user->setUsername($un);
        }
        // See if this user exists in the database
        if ($user->userExistsByUsername() === false)
        {

            // set the information for the user
            if (property_exists($data, 'wNumber') && property_exists($data, 'firstName') &&
                property_exists($data, 'lastName') && property_exists($data, 'gender') &&
                property_exists($data, 'birthDate') && property_exists($data, 'maritalStatus') &&
                property_exists($data, 'address') && property_exists($data, 'city') &&
                property_exists($data, 'state') && property_exists($data, 'zip') &&
                property_exists($data, 'homePhone') && property_exists($data, 'citizenship'))
            {
                $user->setwNumber(filter_var($data->wNumber, FILTER_SANITIZE_STRING));
                $user->setFirstName(filter_var($data->firstName, FILTER_SANITIZE_STRING));
                $user->setLastName(filter_var($data->lastName, FILTER_SANITIZE_STRING));
                $user->setGender(filter_var($data->gender, FILTER_SANITIZE_STRING));
                $user->setBirthDate(filter_var($data->birthDate, FILTER_SANITIZE_STRING));
                $user->setMaritalStatus(filter_var($data->maritalStatus, FILTER_SANITIZE_STRING));
                $user->setAddress(filter_var($data->address, FILTER_SANITIZE_STRING));
                $user->setCity(filter_var($data->city, FILTER_SANITIZE_STRING));
                $user->setState(filter_var($data->state, FILTER_SANITIZE_STRING));
                $user->setZipCode(filter_var($data->zip, FILTER_SANITIZE_NUMBER_INT));
                $user->setHomePhone(filter_var($data->homePhone, FILTER_SANITIZE_STRING));
                $user->setCitizenship(filter_var($data->citizenship, FILTER_SANITIZE_STRING));

                // Set role and username based on the information from token
                $user->setRole($rl);
                $user->setUsername($un);

                if (property_exists($data, 'cellPhone')) {
                    $user->setCellPhone(filter_var($data->cellPhone, FILTER_SANITIZE_NUMBER_INT));
                } else {
                    $user->setCellPhone(null);
                }

                if (property_exists($data, 'secondAddress')) {
                    $user->setSecondAddress(filter_var($data->secondAddress, FILTER_SANITIZE_STRING));
                } else {
                    $user->setSecondAddress(null);
                }

                if (property_exists($data, 'middleInitial')) {
                    $user->setMiddleInitial(filter_var($data->middleInitial, FILTER_SANITIZE_STRING));
                } else {
                    $user->setMiddleInitial(null);
                }

            } else
            {
                http_response_code(StatusCodes::BAD_REQUEST);
                die("Error: the request does not provide enough information to create user");
            }
            //set attributes for the student if it is a student
            if ($rl == User::ROLE_STUDENT)
            {
                //student portion of the new information
                if (property_exists($data, 'overallGPA')) {
                    $user->setOverallGPA(filter_var($data->overallGPA, FILTER_SANITIZE_STRING));
                } else {
                    http_response_code(StatusCodes::BAD_REQUEST);
                    die("Error: required overall GPA is not found");
                }

                // Set optional attributes that are passed in
                if (property_exists($data, 'majorGPA'))
                {
                    $user->setMajorGPA(filter_var($data->majorGPA, FILTER_SANITIZE_STRING));
                } else {
                    $user->setMajorGPA(null);
                }

                if (property_exists($data, 'ACTScore'))
                {
                    $user->setActScore(filter_var($data->ACTScore, FILTER_SANITIZE_NUMBER_INT));
                } else {
                    $user->setActScore(null);
                }

                if (property_exists($data, 'currentMajor')) {
                    $user->setCurrentMajor(filter_var($data->currentMajor, FILTER_SANITIZE_STRING));
                } else {
                    $user->setCurrentMajor(null);
                }

                if (property_exists($data, 'futureMajor')) {
                    $user->setFutureMajor(filter_var($data->futureMajor, FILTER_SANITIZE_STRING));
                } else {
                    $user->setFutureMajor(null);
                }

                if (property_exists($data, 'currentAcademicLevel')) {
                    $user->setCurrentAcademicLevel(filter_var($data->currentAcademicLevel, FILTER_SANITIZE_STRING));
                } else {
                    $user->setCurrentAcademicLevel(null);
                }

                if (property_exists($data, 'degreeGoal')) {
                    $user->setDegreeGoal(filter_var($data->degreeGoal, FILTER_SANITIZE_STRING));
                } else {
                    $user->setDegreeGoal(null);
                }

                if (property_exists($data, 'highSchool')) {
                    $user->setHighSchool(filter_var($data->highSchool, FILTER_SANITIZE_STRING));
                } else {
                    $user->setHighSchool(null);
                }

                if (property_exists($data, 'previousUniversity')) {
                    $user->setPreviousUniversity(filter_var($data->previousUniversity, FILTER_SANITIZE_STRING));
                } else {
                    $user->setPreviousUniversity(null);
                }

                if (property_exists($data, 'firstWSUSemester')) {
                    $user->setFirstSemester(filter_var($data->firstWSUSemester, FILTER_SANITIZE_STRING));
                } else {
                    $user->setFirstSemester(null);
                }

                if (property_exists($data, 'firstWSUYear')) {
                    $user->setFirstYear(filter_var($data->firstWSUYear, FILTER_SANITIZE_NUMBER_INT));
                } else {
                    $user->setFirstYear(null);
                }

                if (property_exists($data, 'scheduleStatus')) {
                    $user->setScheduleStatus(filter_var($data->scheduleStatus, FILTER_SANITIZE_STRING));
                } else {
                    $user->setScheduleStatus(null);
                }

                if (property_exists($data, 'clubOrganizations')) {
                    $user->setClubsOrganizations(filter_var($data->clubOrganizations, FILTER_SANITIZE_STRING));
                } else {
                    $user->setClubsOrganizations(null);
                }

                if (property_exists($data, 'honorAwards')) {
                    $user->setHonorsAwards(filter_var($data->honorAwards, FILTER_SANITIZE_STRING));
                } else {
                    $user->setHonorsAwards(null);
                }

                if (property_exists($data, 'csTopicInterests')) {
                    $user->setCsTopicInterests(filter_var($data->csTopicInterests, FILTER_SANITIZE_STRING));
                } else {
                    $user->setCsTopicInterests(null);
                }

                if (property_exists($data, 'pastScholarshipFinancialAid')) {
                    $user->setPastScholarshipFinancialAid(filter_var($data->pastScholarshipFinancialAid, FILTER_SANITIZE_STRING));
                } else {
                    $user->setPastScholarshipFinancialAid(null);
                }

                if (property_exists($data, 'greatestAchievement')) {
                    $user->setGreatestAchievement(filter_var($data->greatestAchievement, FILTER_SANITIZE_STRING));
                } else {
                    $user->setGreatestAchievement(null);
                }

                if (property_exists($data, 'previousCourses')) {
                    if (!is_array($data->previousCourses)) {
                        http_response_code(StatusCodes::BAD_REQUEST);
                        die("Error: previous courses should be a list of course numbers.");
                    }
                    $user->setPreviousCourses($data->previousCourses);
                } else {
                    $user->setPreviousCourses(null);
                }

                if (property_exists($data, 'currentCourses')) {
                    if (!is_array($data->currentCourses)) {
                        http_response_code(StatusCodes::BAD_REQUEST);
                        die("Error: current courses should be a list of course numbers.");
                    }
                    $user->setCurrentCourses($data->currentCourses);
                } else {
                    $user->setCurrentCourses(null);
                }

                if (property_exists($data, 'apTests')) {
                    if (!is_array($data->apTests)) {
                        http_response_code(StatusCodes::BAD_REQUEST);
                        die("Error: passed ap tests should be a list of ap tests.");
                    }
                    $user->setApTests($data->apTests);
                } else {
                    $user->setApTests(null);
                }
            }
        } else {
            http_response_code(StatusCodes::BAD_REQUEST);
            die('Error: user already exists.');
        }
        //now that the user/student is done being assigned use create() to push it to the DB.
        try {
            $user->create();
        } catch (\Exception $e) {
            http_response_code(StatusCodes::INTERNAL_SERVER_ERROR);
            die($e->getMessage());
        }
    }

    /*
     *  @Route("/users/{id}")
     *  @Method("PATCH")
     */
    function updateUser($args)
    {
        $data = (object) json_decode(file_get_contents('php://input'), true);
        $user = new User();
        $id = filter_var($args['id'], FILTER_SANITIZE_NUMBER_INT);

        if(!$id)
        {
            http_response_code(StatusCodes::BAD_REQUEST);
            die('Error on user update: Invalid WNumber.');
        }

        //Load user based on given WNumber
        $user->setWNumber($id);
        $user->load();

        //After loading, verify role. If user is a student, create and load Student object.
        if($user->getRole() == User::ROLE_STUDENT){
            $user = new Student();
            $user->setWNumber($id);
            $user->load();
        }

        //Check if the retrieved username matches the Token's information
        if($user->getUsername() != Token::getUsernameFromToken()){
            http_response_code(StatusCodes::UNAUTHORIZED);
            die('Error: You are not authorized to complete the requested changes.');
        }

        //Update object properties
        //  This checks each property (much like a full update) to update only what is in the request.
        //  If the property is not in the request, it will retain the original value.
        //  Updating an existing object with only some properties set will create NULL fields in the associated DB row after the update.

        //Update User object with passed in values
        if(property_exists($data, 'firstName')){
            $user->setFirstName(filter_var($data->firstName, FILTER_SANITIZE_STRING));
        }

        if(property_exists($data, 'lastName')){
            $user->setLastName(filter_var($data->lastName, FILTER_SANITIZE_STRING));
        }

        if(property_exists($data, 'middleInitial')){
            $user->setMiddleInitial(filter_var($data->middleInitial, FILTER_SANITIZE_STRING));
        }

        if(property_exists($data, 'birthDate')){
            $user->setBirthDate(filter_var($data->birthDate, FILTER_SANITIZE_STRING));
        }

        if(property_exists($data, 'address')){
            $user->setAddress(filter_var($data->address, FILTER_SANITIZE_STRING));
        }

        if(property_exists($data, 'secondAddress')){
            $user->setSecondAddress(filter_var($data->secondAddress, FILTER_SANITIZE_STRING));
        }

        if(property_exists($data, 'city')){
            $user->setCity(filter_var($data->city, FILTER_SANITIZE_STRING));
        }

        if(property_exists($data, 'state')){
            $user->setState(filter_var($data->state, FILTER_SANITIZE_STRING));
        }

        if(property_exists($data, 'zipCode')){
            $user->setZipCode(filter_var($data->zipCode, FILTER_SANITIZE_STRING));
        }

        if(property_exists($data, 'gender')){
            $user->setGender(filter_var($data->gender, FILTER_SANITIZE_STRING));
        }

        if(property_exists($data, 'maritalStatus')){
            $user->setMaritalStatus(filter_var($data->maritalStatus, FILTER_SANITIZE_STRING));
        }

        if(property_exists($data, 'homePhone')){
            $user->setHomePhone(filter_var($data->homePhone, FILTER_SANITIZE_STRING));
        }

        if(property_exists($data, 'cellPhone')){
            $user->setCellPhone(filter_var($data->cellPhone, FILTER_SANITIZE_STRING));
        }

        if(property_exists($data, 'citizenship')){
            $user->setCitizenship(filter_var($data->citizenship, FILTER_SANITIZE_STRING));
        }

        //If this is a student, check for any updates to the student profile.
        if($user->getRole() == User::ROLE_STUDENT) {
            if(property_exists($data, 'overallGPA')){
                $user->setOverallGPA(filter_var($data->overallGPA, FILTER_SANITIZE_STRING));
            }
            if(property_exists($data, 'majorGPA')){
                $user->setMajorGPA(filter_var($data->majorGPA, FILTER_SANITIZE_STRING));
            }
            if(property_exists($data, 'ACTScore')){
                $user->setActScore(filter_var($data->ACTScore, FILTER_SANITIZE_STRING));
            }
            if(property_exists($data, 'currentMajor')){
                $user->setCurrentMajor(filter_var($data->currentMajor, FILTER_SANITIZE_STRING));
            }
            if(property_exists($data, 'futureMajor')){
                $user->setFutureMajor(filter_var($data->futureMajor, FILTER_SANITIZE_STRING));
            }
            if(property_exists($data, 'currentAcademicLevel')){
                $user->setCurrentAcademicLevel(filter_var($data->currentAcademicLevel, FILTER_SANITIZE_STRING));
            }
            if(property_exists($data, 'degreeGoal')){
                $user->setDegreeGoal(filter_var($data->degreeGoal, FILTER_SANITIZE_STRING));
            }
            if(property_exists($data, 'highSchool')){
                $user->setHighSchool(filter_var($data->highSchool, FILTER_SANITIZE_STRING));
            }
            if(property_exists($data, 'previousUniversity')){
                $user->setPreviousUniversity(filter_var($data->previousUniversity, FILTER_SANITIZE_STRING));
            }
            if(property_exists($data, 'firstWSUSemester')){
                $user->setFirstSemester(filter_var($data->firstWSUSemester, FILTER_SANITIZE_STRING));
            }
            if(property_exists($data, 'firstWSUYear')){
                $user->setFirstYear(filter_var($data->firstWSUYear, FILTER_SANITIZE_STRING));
            }
            if(property_exists($data, 'scheduleStatus')){
                $user->setScheduleStatus(filter_var($data->scheduleStatus, FILTER_SANITIZE_STRING));
            }
            if(property_exists($data, 'clubsOrganizations')){
                $user->setClubsOrganizations(filter_var($data->clubsOrganizations, FILTER_SANITIZE_STRING));
            }
            if(property_exists($data, 'honorsAwards')){
                $user->setHonorsAwards(filter_var($data->honorsAwards, FILTER_SANITIZE_STRING));
            }
            if(property_exists($data, 'csTopicInterests')){
                $user->setCsTopicInterests(filter_var($data->csTopicInterests, FILTER_SANITIZE_STRING));
            }
            if(property_exists($data, 'pastScholarshipFinancialAid')){
                $user->setPastScholarshipFinancialAid(filter_var($data->pastScholarshipFinancialAid, FILTER_SANITIZE_STRING));
            }
            if(property_exists($data, 'greatestAchievement')){
                $user->setGreatestAchievement(filter_var($data->greatestAchievement, FILTER_SANITIZE_STRING));
            }
            if(property_exists($data, 'previousCourses')){
                if(!is_array($data->previousCourses)){
                    http_response_code(StatusCodes::BAD_REQUEST);
                    die("Error: Expected element previousCourses to be an array.");
                }
                $user->setPreviousCourses(filter_var($data->previousCourses, FILTER_SANITIZE_STRING));
            }
            if(property_exists($data, 'currentCourses')){
                if(!is_array($data->currentCourses)){
                    http_response_code(StatusCodes::BAD_REQUEST);
                    die("Error: Expected element currentCourses to be an array.");
                }
                $user->setCurrentCourses(filter_var($data->currentCourses, FILTER_SANITIZE_STRING));
            }
            if(property_exists($data, 'apTests')){
                if(!is_array($data->apTests)){
                    http_response_code(StatusCodes::BAD_REQUEST);
                    die("Error: Expected element apTests to be an array.");
                }
                $user->setApTests(filter_var($data->apTests, FILTER_SANITIZE_STRING));
            }
        }

        //Call the update methods for the User and Student models, saving any changes.
        $user->update();
    }

    /*
     *  @Route("/users/{id}")
     *  @Method("PUT")
     */
    function fullUpdateUser($args)
    {
        $data = (object)json_decode(file_get_contents('php://input'), true);

        if (!filter_var($args['id'], FILTER_SANITIZE_NUMBER_INT))
        {
            http_response_code(StatusCodes::BAD_REQUEST);
            die("Error: wnumber is missing or invalid");
        }

        // Figure out who sent the request
        $username = Token::getUsernameFromToken();
        $role = Token::getRoleFromToken();

        // Set up a user object
        $user = new User();
        $user->setWNumber($args['id']);

        // See if this user exists in the database
        if ($user->userExists())
        {
            $user->load();

            // When the person who sent the request is not matching with the user that he/she is trying to make changes to
            if ($user->getUsername() != $username)
            {
                http_response_code(StatusCodes::UNAUTHORIZED);
                die("Error: the user who sent the request is not authorized to made the changes.");
            }
            else
            {
                if ($user->getRole() == User::ROLE_STUDENT) {
                    $user = new Student();
                    $user->setWNumber($args['id']);
                    $user->load();
                }

                if (property_exists($data, 'firstName') &&
                    property_exists($data, 'lastName') &&
                    property_exists($data, 'gender') &&
                    property_exists($data, 'birthDate') &&
                    property_exists($data, 'maritalStatus') &&
                    property_exists($data, 'address') &&
                    property_exists($data, 'city') &&
                    property_exists($data, 'state') &&
                    property_exists($data, 'homePhone') &&
                    property_exists($data, 'zip') &&
                    property_exists($data, 'citizenship')) {
                    // Set the required basic information
                    $user->setFirstName(filter_var($data->firstName, FILTER_SANITIZE_STRING));
                    $user->setLastName(filter_var($data->lastName, FILTER_SANITIZE_STRING));
                    $user->setGender(filter_var($data->gender, FILTER_SANITIZE_STRING));
                    $user->setBirthDate(filter_var($data->birthDate, FILTER_SANITIZE_STRING));
                    $user->setMaritalStatus(filter_var($data->maritalStatus, FILTER_SANITIZE_STRING));
                    $user->setAddress(filter_var($data->address, FILTER_SANITIZE_STRING));
                    $user->setCity(filter_var($data->city, FILTER_SANITIZE_STRING));
                    $user->setState(filter_var($data->state, FILTER_SANITIZE_STRING));
                    $user->setZipCode(filter_var($data->zip, FILTER_SANITIZE_NUMBER_INT));
                    $user->setHomePhone(filter_var($data->homePhone, FILTER_SANITIZE_STRING));
                    $user->setCitizenship(filter_var($data->citizenship, FILTER_SANITIZE_STRING));

                    $user->setRole($role);
                    $user->setUsername($username);

                    // Set the optional basic information
                    if (property_exists($data, 'cellPhone')) {
                        $user->setCellPhone(filter_var($data->cellPhone, FILTER_SANITIZE_NUMBER_INT));
                    } else {
                        $user->setCellPhone(null);
                    }

                    if (property_exists($data, 'secondAddress')) {
                        $user->setSecondAddress(filter_var($data->secondAddress, FILTER_SANITIZE_STRING));
                    } else {
                        $user->setSecondAddress(null);
                    }

                    if (property_exists($data, 'middleInitial')) {
                        $user->setMiddleInitial(filter_var($data->middleInitial, FILTER_SANITIZE_STRING));
                    } else {
                        $user->setMiddleInitial(null);
                    }
                } else {
                    http_response_code(StatusCodes::BAD_REQUEST);
                    die("Error: the request does not provide enough info for full update");
                }

                // Look for additional attributes when this user is a Student
                if ($user->getRole() == User::ROLE_STUDENT) {
                    if (property_exists($data, 'overallGPA')) {
                        $user->setOverallGPA(filter_var($data->overallGPA, FILTER_SANITIZE_STRING));
                    } else {
                        http_response_code(StatusCodes::BAD_REQUEST);
                        die("Error: required overall GPA is not found");
                    }

                    // Set optional attributes that are passed in
                    if (property_exists($data, 'majorGPA'))
                    {
                        $user->setMajorGPA(filter_var($data->majorGPA, FILTER_SANITIZE_STRING));
                    } else {
                        $user->setMajorGPA(null);
                    }

                    if (property_exists($data, 'ACTScore')) {
                        $user->setActScore(filter_var($data->ACTScore, FILTER_SANITIZE_NUMBER_INT));
                    } else {
                        $user->setActScore(null);
                    }

                    if (property_exists($data, 'currentMajor')) {
                        $user->setCurrentMajor(filter_var($data->currentMajor, FILTER_SANITIZE_STRING));
                    } else {
                        $user->setCurrentMajor(null);
                    }

                    if (property_exists($data, 'futureMajor')) {
                        $user->setFutureMajor(filter_var($data->futureMajor, FILTER_SANITIZE_STRING));
                    } else {
                        $user->setFutureMajor(null);
                    }

                    if (property_exists($data, 'currentAcademicLevel')) {
                        $user->setCurrentAcademicLevel(filter_var($data->currentAcademicLevel, FILTER_SANITIZE_STRING));
                    } else {
                        $user->setCurrentAcademicLevel(null);
                    }

                    if (property_exists($data, 'degreeGoal')) {
                        $user->setDegreeGoal(filter_var($data->degreeGoal, FILTER_SANITIZE_STRING));
                    } else {
                        $user->setDegreeGoal(null);
                    }

                    if (property_exists($data, 'highSchool')) {
                        $user->setHighSchool(filter_var($data->highSchool, FILTER_SANITIZE_STRING));
                    } else {
                        $user->setHighSchool(null);
                    }

                    if (property_exists($data, 'previousUniversity')) {
                        $user->setPreviousUniversity(filter_var($data->previousUniversity, FILTER_SANITIZE_STRING));
                    } else {
                        $user->setPreviousUniversity(null);
                    }

                    if (property_exists($data, 'firstWSUSemester')) {
                        $user->setFirstSemester(filter_var($data->firstWSUSemester, FILTER_SANITIZE_STRING));
                    } else {
                        $user->setFirstSemester(null);
                    }

                    if (property_exists($data, 'firstWSUYear')) {
                        $user->setFirstYear(filter_var($data->firstWSUYear, FILTER_SANITIZE_NUMBER_INT));
                    } else {
                        $user->setFirstYear(null);
                    }

                    if (property_exists($data, 'scheduleStatus')) {
                        $user->setScheduleStatus(filter_var($data->scheduleStatus, FILTER_SANITIZE_STRING));
                    } else {
                        $user->setScheduleStatus(null);
                    }

                    if (property_exists($data, 'clubOrganizations')) {
                        $user->setClubsOrganizations(filter_var($data->clubOrganizations, FILTER_SANITIZE_STRING));
                    } else {
                        $user->setClubsOrganizations(null);
                    }

                    if (property_exists($data, 'honorAwards')) {
                        $user->setHonorsAwards(filter_var($data->honorAwards, FILTER_SANITIZE_STRING));
                    } else {
                        $user->setHonorsAwards(null);
                    }

                    if (property_exists($data, 'csTopicInterests')) {
                        $user->setCsTopicInterests(filter_var($data->csTopicInterests, FILTER_SANITIZE_STRING));
                    } else {
                        $user->setCsTopicInterests(null);
                    }

                    if (property_exists($data, 'pastScholarshipFinancialAid')) {
                        $user->setPastScholarshipFinancialAid(filter_var($data->pastScholarshipFinancialAid, FILTER_SANITIZE_STRING));
                    } else {
                        $user->setPastScholarshipFinancialAid(null);
                    }

                    if (property_exists($data, 'greatestAchievement')) {
                        $user->setGreatestAchievement(filter_var($data->greatestAchievement, FILTER_SANITIZE_STRING));
                    } else {
                        $user->setGreatestAchievement(null);
                    }

                    if (property_exists($data, 'previousCourses')) {
                        if (!is_array($data->previousCourses)) {
                            http_response_code(StatusCodes::BAD_REQUEST);
                            die("Error: previous courses should be a list of course numbers.");
                        }
                        $user->setPreviousCourses($data->previousCourses);
                    } else {
                        $user->setPreviousCourses(null);
                    }

                    if (property_exists($data, 'currentCourses')) {
                        if (!is_array($data->currentCourses)) {
                            http_response_code(StatusCodes::BAD_REQUEST);
                            die("Error: current courses should be a list of course numbers.");
                        }
                        $user->setCurrentCourses($data->currentCourses);
                    } else {
                        $user->setCurrentCourses(null);
                    }

                    if (property_exists($data, 'apTests')) {
                        if (!is_array($data->apTests)) {
                            http_response_code(StatusCodes::BAD_REQUEST);
                            die("Error: passed ap tests should be a list of ap tests.");
                        }
                        $user->setApTests($data->apTests);
                    } else {
                        $user->setApTests(null);
                    }
                }

                // Save the changes to database
                try {
                    $user->update();
                }
                catch (\Exception $e)
                {
                    http_response_code(StatusCodes::INTERNAL_SERVER_ERROR);
                }
            }
        }
        else
        {
            http_response_code(StatusCodes::BAD_REQUEST);
        }

    }

    /*
     *  @Route("/users/{id}")
     *  @Method("DELETE")
     */
    function deleteUser($args)
    {
        if (!filter_var($args['id'], FILTER_SANITIZE_NUMBER_INT))
        {
            http_response_code(StatusCodes::BAD_REQUEST);
            die("Error: wnumber is missing or invalid");
        }

        if ($role = Token::getRoleFromToken() == User::ROLE_FACULTY) {

            $user = new User();
            $user->setWNumber($args['id']);
            if ($user->userExists()) {
                $user->load();

                if ($user->getRole() == User::ROLE_FACULTY && $user->getUsername() != Token::getUsernameFromToken())
                {
                    http_response_code(StatusCodes::UNAUTHORIZED);
                    die("Error: you are not authorized to send this request");
                }
                elseif ($user->getRole() == User::ROLE_STUDENT) {
                    $user = new Student();
                    $user->setWNumber($args['id']);
                    $user->load();
                }

                try {
                    $user->delete();
                } catch (\Exception $e) {
                    http_response_code(StatusCodes::INTERNAL_SERVER_ERROR);
                }
            }

        }
        else
        {
            http_response_code(StatusCodes::UNAUTHORIZED);
        }
    }

    function getAllStudents (){
        return $this->getAllForRole(User::ROLE_STUDENT);
    }

    function getAllFaculty (){
        return $this->getAllForRole(User::ROLE_FACULTY);
    }

    //Gets all users that match a given role.
    //Will only return if authenticated user is a faculty member.
    private function getAllForRole($role)
    {
        if(TOKEN::getRoleFromToken() != Token::ROLE_FACULTY)
        {
            http_response_code(StatusCodes::FORBIDDEN);
        }

        $dbh = DatabaseConnection::getInstance();

        //Prep the query based on the requested role
        $stmt = $dbh->prepare("SELECT wNumber FROM `User` WHERE role = :role");
        $stmt->bindValue(":role", $role);
        $stmt->setFetchMode(\PDO::FETCH_ASSOC);
        $success = $stmt->execute();

        if(!$success)
        {
            die("Error: sql query execution failed.");
        }

        // Retrieve a list of users with corresponding role
        $arr = array();
        while ($wNumber = $stmt->fetch()['wNumber'])
        {
            $user = new User();
            if ($role == User::ROLE_STUDENT)
                $user = new Student();

            $user->setWNumber($wNumber);
            $user->load();
            array_push($arr, $user);
        }

        return $arr;
    }


}