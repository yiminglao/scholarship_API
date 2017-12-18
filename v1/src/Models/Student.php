<?php
/**
 * Created by PhpStorm.
 * User: kolefrazier
 * Date: 10/31/2017
 * Time: 4:01 PM
 */

namespace Scholarship\Models;


use Scholarship\Utilities\DatabaseConnection;

class Student Extends User implements \JsonSerializable
{
    // Constants
    const PREVIOUS_COURSE = 0;
    const CURRENT_COURSE = 1;

    //Attributes
    private $overallGPA;
    private $majorGPA;
    private $actScore;
    private $currentMajor;
    private $futureMajor;
    private $currentAcademicLevel;
    private $degreeGoal;
    private $highSchool;
    private $previousUniversity;
    private $firstSemester;
    private $firstYear;
    private $scheduleStatus;
    private $clubsOrganizations;
    private $honorsAwards;
    private $csTopicInterests;
    private $pastScholarshipFinancialAid;
    private $greatestAchievement;

    # Multi-value attributes
    private $previousCourses;
    private $currentCourses;
    private $apTests;

    public function __construct()
    {

    }

    //Getters and Setters
    /**
     * @return mixed
     */
    public function getOverallGPA()
    {
        return $this->overallGPA;
    }

    /**
     * @param mixed $overallGPA
     */
    public function setOverallGPA($overallGPA)
    {
        $this->overallGPA = $overallGPA;
    }

    /**
     * @return mixed
     */
    public function getMajorGPA()
    {
        return $this->majorGPA;
    }

    /**
     * @param mixed $majorGPA
     */
    public function setMajorGPA($majorGPA)
    {
        $this->majorGPA = $majorGPA;
    }

    /**
     * @return mixed
     */
    public function getActScore()
    {
        return $this->actScore;
    }

    /**
     * @param mixed $actScore
     */
    public function setActScore($actScore)
    {
        $this->actScore = $actScore;
    }

    /**
     * @return mixed
     */
    public function getCurrentMajor()
    {
        return $this->currentMajor;
    }

    /**
     * @param mixed $currentMajor
     */
    public function setCurrentMajor($currentMajor)
    {
        $this->currentMajor = $currentMajor;
    }

    /**
     * @return mixed
     */
    public function getFutureMajor()
    {
        return $this->futureMajor;
    }

    /**
     * @param mixed $futureMajor
     */
    public function setFutureMajor($futureMajor)
    {
        $this->futureMajor = $futureMajor;
    }

    /**
     * @return mixed
     */
    public function getCurrentAcademicLevel()
    {
        return $this->currentAcademicLevel;
    }

    /**
     * @param mixed $currentAcademicLevel
     */
    public function setCurrentAcademicLevel($currentAcademicLevel)
    {
        $this->currentAcademicLevel = $currentAcademicLevel;
    }

    /**
     * @return mixed
     */
    public function getDegreeGoal()
    {
        return $this->degreeGoal;
    }

    /**
     * @param mixed $degreeGoal
     */
    public function setDegreeGoal($degreeGoal)
    {
        $this->degreeGoal = $degreeGoal;
    }

    /**
     * @return mixed
     */
    public function getHighSchool()
    {
        return $this->highSchool;
    }

    /**
     * @param mixed $highSchool
     */
    public function setHighSchool($highSchool)
    {
        $this->highSchool = $highSchool;
    }

    /**
     * @return mixed
     */
    public function getPreviousUniversity()
    {
        return $this->previousUniversity;
    }

    /**
     * @param mixed $previousUniversity
     */
    public function setPreviousUniversity($previousUniversity)
    {
        $this->previousUniversity = $previousUniversity;
    }

    /**
     * @return mixed
     */
    public function getFirstSemester()
    {
        return $this->firstSemester;
    }

    /**
     * @param mixed $firstSemester
     */
    public function setFirstSemester($firstSemester)
    {
        $this->firstSemester = $firstSemester;
    }

    /**
     * @return mixed
     */
    public function getFirstYear()
    {
        return $this->firstYear;
    }

    /**
     * @param mixed $firstYear
     */
    public function setFirstYear($firstYear)
    {
        $this->firstYear = $firstYear;
    }

    /**
     * @return mixed
     */
    public function getScheduleStatus()
    {
        return $this->scheduleStatus;
    }

    /**
     * @param mixed $scheduleStatus
     */
    public function setScheduleStatus($scheduleStatus)
    {
        $this->scheduleStatus = $scheduleStatus;
    }

    /**
     * @return mixed
     */
    public function getClubsOrganizations()
    {
        return $this->clubsOrganizations;
    }

    /**
     * @param mixed $clubsOrganizations
     */
    public function setClubsOrganizations($clubsOrganizations)
    {
        $this->clubsOrganizations = $clubsOrganizations;
    }

    /**
     * @return mixed
     */
    public function getHonorsAwards()
    {
        return $this->honorsAwards;
    }

    /**
     * @param mixed $honorsAwards
     */
    public function setHonorsAwards($honorsAwards)
    {
        $this->honorsAwards = $honorsAwards;
    }

    /**
     * @return mixed
     */
    public function getCsTopicInterests()
    {
        return $this->csTopicInterests;
    }

    /**
     * @param mixed $csTopicInterests
     */
    public function setCsTopicInterests($csTopicInterests)
    {
        $this->csTopicInterests = $csTopicInterests;
    }

    /**
     * @return mixed
     */
    public function getPastScholarshipFinancialAid()
    {
        return $this->pastScholarshipFinancialAid;
    }

    /**
     * @param mixed $pastScholarshipFinancialAid
     */
    public function setPastScholarshipFinancialAid($pastScholarshipFinancialAid)
    {
        $this->pastScholarshipFinancialAid = $pastScholarshipFinancialAid;
    }

    /**
     * @return mixed
     */
    public function getGreatestAchievement()
    {
        return $this->greatestAchievement;
    }

    /**
     * @param mixed $greatestAchievement
     */
    public function setGreatestAchievement($greatestAchievement)
    {
        $this->greatestAchievement = $greatestAchievement;
    }

    /**
     * @return array
     */
    public function getPreviousCourses()
    {
        return $this->previousCourses;
    }

    /**
     * @param array $previousCourses
     */
    public function setPreviousCourses($previousCourses)
    {
        $this->previousCourses = $previousCourses;
    }

    /**
     * @return array
     */
    public function getCurrentCourses()
    {
        return $this->currentCourses;
    }

    /**
     * @param array $currentCourses
     */
    public function setCurrentCourses($currentCourses)
    {
        $this->currentCourses = $currentCourses;
    }

    /**
     * @return array
     */
    public function getApTests()
    {
        return $this->apTests;
    }

    /**
     * @param array $apTests
     */
    public function setApTests($apTests)
    {
        $this->apTests = $apTests;
    }

    //Database Methods
    public function create()
    {
        try
        {
            // Create a user in the user table
            parent::create();

            $dbh = DatabaseConnection::getInstance();

            // Prepare sql statement to create a student in the student table
            $stmtHandle = $dbh->prepare(
                "INSERT INTO `Student`(
                `wNumber`, 
                `overallGPA`, 
                `majorGPA`, 
                `ACTScore`, 
                `currentMajor`, 
                `futureMajor`, 
                `currentAcademicLevel`, 
                `degreeGoal`, 
                `highSchool`, 
                `previousUniversity`, 
                `firstWSUSemester`, 
                `firstWSUYear`, 
                `scheduleStatus`, 
                `clubOrganizations`, 
                `honorsAwards`, 
                `csTopicInterests`, 
                `pastScholarshipFinancialAid`, 
                `greatestAchievement`) 
                VALUES (:wNumber,:overallGPA,:majorGPA,:actScore,:currentMajor,:futureMajor,:currentAcademicLevel,
                :degreeGoal,:highSchool,:previousUniversity,:firstWSUSemester,:firstWSUYear,:scheduleStatus,
                :clubOrganizations,:honorsAwards,:csTopicInterests,:pastScholarshipFinancialAid,:greatestAchievement)");

            // Bind the object's attribute values to the sql query
            $stmtHandle->bindValue(":wNumber", $this->getWNumber());
            $stmtHandle->bindValue(":overallGPA", $this->overallGPA);
            $stmtHandle->bindValue(":majorGPA", $this->majorGPA);
            $stmtHandle->bindValue(":actScore", $this->actScore);
            $stmtHandle->bindValue(":currentMajor", $this->currentMajor);
            $stmtHandle->bindValue(":futureMajor", $this->futureMajor);
            $stmtHandle->bindValue(":currentAcademicLevel", $this->currentAcademicLevel);
            $stmtHandle->bindValue(":degreeGoal", $this->degreeGoal);
            $stmtHandle->bindValue(":highSchool", $this->highSchool);
            $stmtHandle->bindValue(":previousUniversity", $this->previousUniversity);
            $stmtHandle->bindValue(":firstWSUSemester", $this->firstSemester);
            $stmtHandle->bindValue(":firstWSUYear", $this->firstYear);
            $stmtHandle->bindValue(":scheduleStatus", $this->scheduleStatus);
            $stmtHandle->bindValue(":clubOrganizations", $this->clubsOrganizations);
            $stmtHandle->bindValue(":honorsAwards", $this->honorsAwards);
            $stmtHandle->bindValue(":csTopicInterests", $this->csTopicInterests);
            $stmtHandle->bindValue(":pastScholarshipFinancialAid", $this->pastScholarshipFinancialAid);
            $stmtHandle->bindValue(":greatestAchievement", $this->greatestAchievement);

            $success = $stmtHandle->execute();
            if (!$success)
            {
                throw new \PDOException("sql query execution failed");
            }

            // When the current courses info for this student are passed in
            if (!empty($this->currentCourses) && !($this->currentCourses === null))
            {
                $this->insertCourses(self::CURRENT_COURSE);
            }

            // When the previous courses info for this student are passed in
            if (!empty($this->previousCourses) && !($this->previousCourses === null))
            {
                $this->insertCourses(self::PREVIOUS_COURSE);
            }

            // When the AP tests info for this student are passed in
            if (!empty($this->apTests) && !($this->apTests === null))
            {
                $this->insertTests();
            }

        }
        catch (\Exception $e)
        {
            throw $e;
        }

    }

    /*
     * This method updates the corresponding student in the database based on the data that this student object holds
     */
    public function update()
    {
        try
        {
            if (empty($this->getWNumber()))
            {
                die("error: the wnumber is not provided");
            }
            else
            {
                // Update the basic information required for a user
                parent::update();

                $dbh = DatabaseConnection::getInstance();

                // Prepare sql statement to update information for a student
                $stmtHandle = $dbh->prepare(
                    "UPDATE `Student` 
             SET `OverallGPA`= :overallGPA,
                 `MajorGPA`= :majorGPA,
                 `ACTScore`= :actScore,
                 `CurrentMajor`= :currentMajor,
                 `FutureMajor`= :futureMajor,
                 `CurrentAcademicLevel`= :currentAcademicLevel,
                 `DegreeGoal`= :degreeGoal,
                 `HighSchool`= :highSchool,
                 `PreviousUniversity`= :previousUniversity,
                 `FirstWSUSemester`= :firstWSUSemester,
                 `FirstWSUYear`= :firstWSUYear,
                 `ScheduleStatus`= :scheduleStatus,
                 `ClubOrganizations`= :clubOrganizations,
                 `HonorsAwards`= :honorsAwards,
                 `CSTopicInterests`= :csTopicInterests,
                 `PastScholarshipFinancialAid`= :pastScholarshipFinancialAid,
                 `GreatestAchievement`= :greatestAchievement 
             WHERE `wNumber` = :wNumber");

                $stmtHandle->bindValue(":wNumber", $this->getWNumber());
                $stmtHandle->bindValue(":overallGPA", $this->overallGPA);
                $stmtHandle->bindValue(":majorGPA", $this->majorGPA);
                $stmtHandle->bindValue(":actScore", $this->actScore);
                $stmtHandle->bindValue(":currentMajor", $this->currentMajor);
                $stmtHandle->bindValue(":futureMajor", $this->futureMajor);
                $stmtHandle->bindValue(":currentAcademicLevel", $this->currentAcademicLevel);
                $stmtHandle->bindValue(":degreeGoal", $this->degreeGoal);
                $stmtHandle->bindValue(":highSchool", $this->highSchool);
                $stmtHandle->bindValue(":previousUniversity", $this->previousUniversity);
                $stmtHandle->bindValue(":firstWSUSemester", $this->firstSemester);
                $stmtHandle->bindValue(":firstWSUYear", $this->firstYear);
                $stmtHandle->bindValue(":scheduleStatus", $this->scheduleStatus);
                $stmtHandle->bindValue(":clubOrganizations", $this->clubsOrganizations);
                $stmtHandle->bindValue(":honorsAwards", $this->honorsAwards);
                $stmtHandle->bindValue(":csTopicInterests", $this->csTopicInterests);
                $stmtHandle->bindValue(":pastScholarshipFinancialAid", $this->pastScholarshipFinancialAid);
                $stmtHandle->bindValue(":greatestAchievement", $this->greatestAchievement);

                $success = $stmtHandle->execute();

                if (!$success) {
                    throw new \PDOException("user full update operation failed.");
                }

                // Delete all the course records related to this student to avoid redundant primary key in db
                $stmtHandle = $dbh->prepare("DELETE FROM `ConcurrentPastCourse` WHERE `wNumber` = :wNumber");
                $stmtHandle->bindValue(":wNumber", $this->getWNumber());
                $stmtHandle->execute();

                // Delete all the ap tests passed records related to this student to avoid rendundant primary key in db
                $stmtHandle = $dbh->prepare("DELETE FROM `APTestPassed` WHERE `wNumber` = :wNumber");
                $stmtHandle->bindValue(":wNumber", $this->getWNumber());
                $stmtHandle->execute();

                // When the current courses info for this student are passed in
                if (!empty($this->currentCourses) && !($this->currentCourses === null)) {
                    $this->insertCourses(self::CURRENT_COURSE);
                }

                // When the previous courses info for this student are passed in
                if (!empty($this->previousCourses) && !($this->previousCourses === null)) {
                    $this->insertCourses(self::PREVIOUS_COURSE);
                }

                // When the AP tests info for this student are passed in
                if (!empty($this->apTests) && !($this->apTests === null)) {
                    $this->insertTests();
                }
            }
        }
        catch(\PDOException $e)
        {
            throw $e;
        }

    }

    public function delete()
    {
        try
        {
            if (empty($this->getWNumber()))
            {
                die("error: the wnumber is not provided");
            }
            else
            {
                // Delete the corresponding row in the User table
                parent::delete();

                $dbh = DatabaseConnection::getInstance();

                if (!empty($this->apTests)) {
                    $stmtHandle = $dbh->prepare("DELETE FROM `APTestPassed` WHERE `wNumber` = :wNumber");
                    $stmtHandle->bindValue(":wNumber", $this->getWNumber());
                    $stmtHandle->execute();
                }

                if (!empty($this->currentCourses) || !empty($this->previousCourses)) {
                    $stmtHandle = $dbh->prepare("DELETE FROM `ConcurrentPastCourse` WHERE `wNumber` = :wNumber");
                    $stmtHandle->bindValue(":wNumber", $this->getWNumber());
                    $stmtHandle->execute();
                }

                $stmtHandle = $dbh->prepare("DELETE FROM `Student` WHERE `wNumber` = :wNumber");
                $stmtHandle->bindValue(":wNumber", $this->getWNumber());
                $stmtHandle->execute();
            }
        }
        catch (\PDOException $e)
        {
            throw $e;
        }
    }

    /*
     * This method will retrieve the Course Id based on the course number
     */
    private function getCourseId(int $courseNum)
    {
        try {
            $dbh = DatabaseConnection::getInstance();
            $statement = $dbh->prepare("SELECT `courseId` FROM `CSCourse` WHERE `courseNumber` = :courseNum");
            $statement->bindValue(":courseNum", $courseNum);
            $statement->setFetchMode(\PDO::FETCH_ASSOC);
            $success = $statement->execute();

            if (!$success) {
                throw new \PDOException("error: fail to retrieve course id with provided course num");
            }
            else
            {
                $course = $statement->fetch();
                if (!empty($course['courseId']))
                {
                    return $course['courseId'];
                }
                else
                {
                    throw new \PDOException("error: the provided course number is not in the current course list");
                }
            }
        }
        catch (\PDOException $e)
        {
            throw $e;
        }
    }

    /*
     * This method will retrieve the AP Test Id based on the AP Test Name
     */
    private function getApTestId(string $testName)
    {
        try {
            $dbh = DatabaseConnection::getInstance();
            $statement = $dbh->prepare("SELECT `apTestId` FROM `APTest` WHERE apTestName = :apTestName");
            $statement->bindValue(":apTestName", $testName);
            $statement->setFetchMode(\PDO::FETCH_ASSOC);
            $success = $statement->execute();

            if (!$success)
            {
                throw new \PDOException("error: fail to retrieve ap test id with provided test name");
            }
            else
            {
                $test = $statement->fetch();
                if (!empty($test['apTestId']))
                {
                    return $test['apTestId'];
                }
                else
                {
                    throw new \PDOException("error: the provided ap test name is not in the current ap test list");
                }
            }
        }
        catch (\PDOException $e)
        {
            throw $e;
        }
    }

    /*
     * This load method loads the local student object with the data in the database when the wNumber is set
     */
    public function load()
    {
        try
        {
            if (empty(self::getWNumber()))
            {
                die("error: the wnumber is not provided");
            }
            else
            {
                parent::load();
                $dbh = DatabaseConnection::getInstance();
                $stmtHandle = $dbh->prepare("SELECT * FROM `Student` WHERE wNumber = :wNumber");
                $stmtHandle->bindValue(":wNumber", self::getWNumber());

                $stmtHandle->setFetchMode(\PDO::FETCH_ASSOC);
                $success = $stmtHandle->execute();

                if (!$success)
                {
                    throw new \PDOException("error: sql query execution failed.");
                }
                else
                {
                    $user = $stmtHandle->fetch();

                    // Setting the student specific attributes value from the database
                    $this->setOverallGPA($user['overallGPA']);
                    $this->setMajorGPA($user['majorGPA']);
                    $this->setActScore($user['ACTScore']);
                    $this->setCurrentMajor($user['currentMajor']);
                    $this->setFutureMajor($user['futureMajor']);
                    $this->setCurrentAcademicLevel($user['currentAcademicLevel']);
                    $this->setDegreeGoal($user['degreeGoal']);
                    $this->setHighSchool($user['highSchool']);
                    $this->setPreviousUniversity($user['previousUniversity']);
                    $this->setFirstSemester($user['firstWSUSemester']);
                    $this->setFirstYear($user['firstWSUYear']);
                    $this->setScheduleStatus($user['scheduleStatus']);
                    $this->setClubsOrganizations($user['clubOrganizations']);
                    $this->setHonorsAwards($user['honorsAwards']);
                    $this->setCsTopicInterests($user['csTopicInterests']);
                    $this->setPastScholarshipFinancialAid($user['pastScholarshipFinancialAid']);
                    $this->setGreatestAchievement($user['greatestAchievement']);

                    // Prepare sql statement to retrieve the courses info related to this student
                    $stmtHandle = $dbh->prepare("SELECT ConcurrentPastCourse.wNumber, 
                                                        ConcurrentPastCourse.courseStatus, 
                                                        CSCourse.courseNumber 
                                                 FROM `CSCourse` JOIN `ConcurrentPastCourse` 
                                                 ON CSCourse.courseId = ConcurrentPastCourse.courseId 
                                                 WHERE ConcurrentPastCourse.wNumber = :wNumber
                                                 AND ConcurrentPastCourse.courseStatus = :courseStatus");

                    $stmtHandle->bindValue(":wNumber", $this->getWNumber());
                    $stmtHandle->bindValue(":courseStatus", self::CURRENT_COURSE);
                    $success = $stmtHandle->execute();

                    if (!$success)
                    {
                        throw new \PDOException("error: sql query execution failed.");
                    }
                    else
                    {
                        $curr_course_list = array();
                        while ($course = $stmtHandle->fetch())
                        {
                            array_push($curr_course_list, $course['courseNumber']);
                        }
                        self::setCurrentCourses($curr_course_list);
                    }

                    $stmtHandle->bindValue(":courseStatus", self::PREVIOUS_COURSE);
                    $success = $stmtHandle->execute();

                    if (!$success)
                    {
                        throw new \PDOException("error: sql query execution failed.");
                    }
                    else
                    {
                        $prev_course_list = array();
                        while ($course = $stmtHandle->fetch())
                        {
                            array_push($prev_course_list, $course['courseNumber']);
                        }
                        self::setPreviousCourses($prev_course_list);
                    }

                    // Prepare sql statement to retrieve ap test info related to this student
                    $stmtHandle = $dbh->prepare("SELECT APTest.apTestName, APTestPassed.wNumber 
                                                 FROM `APTest` JOIN `APTestPassed` 
                                                 ON APTest.apTestId = APTestPassed.apTestId 
                                                 WHERE APTestPassed.wNumber = :wNumber");
                    $stmtHandle->bindValue(":wNumber", $this->getWNumber());
                    $success = $stmtHandle->execute();

                    if (!$success)
                    {
                        throw new \PDOException("error: sql query execution failed.");
                    }
                    else
                    {
                        $test_list = array();
                        while ($passed_test = $stmtHandle->fetch())
                        {
                            array_push($test_list, $passed_test['apTestName']);
                        }
                        self::setApTests($test_list);
                    }
                }
            }
        }
        catch (\PDOException $e)
        {
            throw $e;
        }
    }

    /*
     * This method insert courses into the association table between student and courses
     */
    private function insertCourses($courseStatus)
    {
        $dbh = DatabaseConnection::getInstance();
        $stmtHandle = $dbh->prepare(
            "INSERT INTO `ConcurrentPastCourse`(`wNumber`, `courseId`, `courseStatus`) 
                     VALUES (:wNumber, :courseId, :courseStatus)");

        // Insert all the courses associated with this student into the database
        $curr_courses = $this->getCurrentCourses();
        if ($courseStatus == self::PREVIOUS_COURSE)
        {
            $curr_courses = $this->getPreviousCourses();
        }

        foreach ($curr_courses as &$courseNum) {
            $courseId = $this->getCourseId($courseNum);
            $stmtHandle->bindValue(":wNumber", $this->getWNumber());
            $stmtHandle->bindValue(":courseId", $courseId);
            $stmtHandle->bindValue(":courseStatus",$courseStatus);
            $stmtHandle->execute();
        }
    }

    /*
     * This method insert tests into the association table between ap test and student
     */
    private function insertTests()
    {
        $dbh = DatabaseConnection::getInstance();
        $stmtHandle = $dbh->prepare(
            "INSERT INTO `APTestPassed`(`wNumber`, `apTestID`) 
                 VALUES (:wNumber, :testId)");

        // Insert all the passed ap test associated with this student into the database
        $passed_tests = $this->getApTests();
        foreach ($passed_tests as &$testName) {
            $test_id = $this->getApTestId($testName);
            $stmtHandle->bindValue(":wNumber", $this->getWNumber());
            $stmtHandle->bindValue(":testId", $test_id);
            $stmtHandle->execute();
        }
    }

    function jsonSerialize()
    {
        
        $arr = parent::jsonSerialize();


        $rtn = array('overallGPA' => $this->overallGPA,
            'majorGPA' => $this->majorGPA,
            'actScore' => $this->actScore,
            'currentMajor' => $this->currentMajor,
            'futureMajor' => $this->futureMajor,
            'currentAcademicLevel' => $this->currentAcademicLevel,
            'degreeGoal' => $this->degreeGoal,
            'highSchool' => $this->highSchool,
            'previousUniversity' => $this->previousUniversity,
            'firstSemester' => $this->firstSemester,
            'firstYear' => $this->firstYear,
            'scheduleStatus' => $this->scheduleStatus,
            'clubsOrganizations' => $this->clubsOrganizations,
            'honorsAward' => $this->honorsAwards,
            'csTopicInterests' => $this->csTopicInterests,
            'pastScholarshipFinancialAid' => $this->pastScholarshipFinancialAid,
            'greatestAchievement' => $this->greatestAchievement,
            'previousCourses' => $this->previousCourses,
            'currentCourse' => $this->currentCourses,
            'apTests' => $this->apTests);


        $fullarr = array_merge($arr, $rtn);
        return $fullarr;
    }
}