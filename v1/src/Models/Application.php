<?php
/**
 * Created by PhpStorm.
 * User: CW
 * Date: 10/30/2017
 * Time: 8:55 AM
 */

namespace Scholarship\Models;

use Scholarship\Utilities\DatabaseConnection as DatabaseConnection;

class Application implements \JsonSerializable

{
    //Attributes
    private $applicationID;
    private $userID;
    private $scholarshipID;
    private $dateCreated;
    private $dateModified;
    private $responses;
    private $dbh;

    public function __construct($applicationID = null, $userID = null, $scholarshipID = null, $dateCreated = null, $dateModified = null)
    {
        $this->setApplicationID($applicationID);
        $this->setUserID($userID);
        $this->setscholarshipID($scholarshipID);
        $this->setDateCreated($dateCreated);
        $this->setDateModified($dateModified);
        $this->dbh = DatabaseConnection::getInstance();
    }

    /**
     * @return mixed
     */
    public function getApplicationID()
    {
        return $this->applicationID;
    }

    /**
     * @return mixed
     */
    public function setApplicationID($applicationID)
    {
        if(isset($applicationID) && !empty($applicationID)) {
            $this->applicationID = $applicationID;
        }
    }

    /**
     * @return mixed
     */
    public function getUserID()
    {
        return $this->userID;
    }

    /**
     * @param mixed $userID
     */
    public function setUserID($userID)
    {
        if(isset($userID) && !empty($userID)) {
            $this->userID = $userID;
        }
    }

    /**
     * @return mixed
     */
    public function getScholarshipID()
    {
        return $this->scholarshipID;
    }

    /**
     * @param mixed $scholarshipID
     */
    public function setScholarshipID($scholarshipID)
    {
        if(isset($scholarshipID) && !empty($scholarshipID)) {
            $this->scholarshipID = $scholarshipID;
        }
    }

    /**
     * @return mixed
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * @param mixed $dateCreated
     */
    public function setDateCreated($dateCreated)
    {
        if(isset($dateCreated) && !empty($dateCreated)) {
            $this->dateCreated = $dateCreated;
        }
    }

    /**
     * @return mixed
     */
    public function getDateModified()
    {
        return $this->dateModified;
    }

    /**
     * @param mixed $dateModified
     */
    public function setDateModified($dateModified)
    {
        if(isset($dateModified) && !empty($dateModified)) {
            $this->dateModified = $dateModified;
        }
    }

    /**
     * @return mixed
     */
    public function getResponses()
    {
        return $this->responses;
    }

    /**
     * @param mixed $responseIDs
     */
    public function setResponses($responses)
    {
        if(isset($responses) && !empty($responses)) {
            $this->responses = $responses;
        }
    }

    //Database Operations

    public function saveApplication()
    {
        try
        {
            $dbh = DatabaseConnection::getInstance();

            if ($this->applicationID == null)
            {
                $stmtHandle = $dbh->prepare("INSERT INTO `application` (userID, scholarshipID) 
                                  VALUES (:userID, :scholarshipID)");
                //$stmtHandle->bindValue("applicationID", $this ->applicationID);
                $stmtHandle->bindValue("userID", $this ->userID);
                $stmtHandle->bindValue("scholarshipID", $this->scholarshipID);

                $stmtHandle->execute();

                //$stmtHandle = $dbh->prepare("SELECT LAST_INSERT_ID() FROM Application");
                $this->applicationID = $dbh->lastInsertID();
            }
            else
            {
                //don't need to update scholarship
            }
        }
        catch (\PDOException $e)
        {
            //print "Error!: " . $e->getMessage() . "</br>";
        }

    }

    /**
     * @param array|object|null $dbRow: An associative array or object containing the key/value pairs of an Application object's properties (i.e. a database row).
     * @return self $this
     */
    public function load($dbRow = null)
    {
        // Query the database if an array/object is not provided, but the applicationID (or userID AND scholarshipID) is set.
        if (is_null($dbRow))
        {
            if (isset($this->applicationID))
            {
                $stmt = $this->dbh->prepare("SELECT * FROM `application` WHERE `applicationID` = :applicationID");
                $stmt->bindParam('applicationID', $this->applicationID);
            }
            elseif (isset($this->userID) && isset($this->scholarshipID))
            {
                $stmt = $this->dbh->prepare("SELECT * FROM `application` WHERE `userID` = :userID AND `scholarshipID` = :scholarshipID");
                $stmt->bindParam('userID', $this->userID);
                $stmt->bindParam('scholarshipID', $this->scholarshipID);
            }
            try
            {
                $stmt->execute();
                $stmt->setFetchMode(\PDO::FETCH_ASSOC);
                $row = $stmt->fetch();
                $this->load($row);
            }
            catch (\PDOException $e)
            {
            }
        }

        // If an array/object is provided, set the properties from the dbRow
        elseif (is_array($dbRow) || is_object($dbRow))
        {
            foreach ($dbRow as $property => $value)
            {
                if (method_exists($this, ($method = 'set' . ucfirst($property)) ))
                {
                    $this->$method($value);
                }
            }
            $this->loadResponses();
        }

        return $this;
    }

    /**
     * Loads the object from the database.
     * @param array $args: an associative array with either a 'applicationID' key/value pair and/or an 'userID' key/value pair.
     */
    static function loadApplications($args = null)
    {
        try {
            $dbh = DatabaseConnection::getInstance();
            if (isset($args['applicationID']) && isset($args['userID'])) {
                $stmt = $dbh->prepare("SELECT * FROM `application` WHERE `applicationID` = :applicationID AND `userID` = :userID");
                $stmt->bindParam('applicationID', $args['applicationID']);
                $stmt->bindParam('userID', $args['userID']);
            }
            else if (isset($args['applicationID'])) {
                $stmt = $dbh->prepare("SELECT * FROM `application` WHERE `applicationID` = :applicationID");
                $stmt->bindParam('applicationID', $args['applicationID']);
            } else if (isset($args['userID'])) {
                $stmt = $dbh->prepare("SELECT * FROM `application` WHERE `userID` = :userID");
                $stmt->bindParam('userID', $args['userID']);
            } else {
                $stmt = $dbh->prepare("SELECT * FROM `application`");
            }

            $stmt->execute();
            $stmt->setFetchMode(\PDO::FETCH_ASSOC);
            $data = $stmt->fetchAll();

            $applications = array();

            foreach ($data as $row) //$propertyName => $value)
            {
                $application = new Application();
                $application->load($row);
                $applications[] = $application;
            }

            return $applications;
        }
        catch (\PDOException $e) {

        }
    }

    public function loadResponses()
    {
        $responses = Response::loadResponses(array('applicationID' => $this->applicationID));
        $this->setResponses($responses);
    }

    function jsonSerialize()
    {
        // TODO: Implement jsonSerialize() method.
        return
            array(
                "applicationID"=> $this->getApplicationID(),
                "userID"=> $this->getUserID(),
                "scholarshipID"=> $this->getScholarshipID(),
                "dateCreated"=> $this->getDateCreated(),
                "dateModified"=> $this->getDateModified(),
                "responses"=> $this->getResponses()
            );
    }


}