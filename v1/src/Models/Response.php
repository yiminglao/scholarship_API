<?php
/**
 * Created by PhpStorm.
 * User: CW
 * Date: 10/30/2017
 * Time: 8:55 AM
 */

namespace Scholarship\Models;


use Scholarship\Utilities\DatabaseConnection;

class Response implements \JsonSerializable
{
    //Attributes
    private $responseID;
    private $questionID;
    private $applicationID;
    private $responseText;
    private $dbh;

    public function __construct($responseID = null, $questionID = null, $applicationID = null, $responseText = null)
    {
        $this->setResponseID($responseID);
        $this->setQuestionID($questionID);
        $this->setResponseText($responseText);
        $this->setApplicationID($applicationID);
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
     * @param mixed $applicationID
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
    public function getResponseID()
    {
        return $this->responseID;
    }

    /**
     * @param mixed $responseID
     */
    public function setResponseID($responseID)
    {
        if(isset($responseID) && !empty($responseID))
        {
            $this->responseID = $responseID;
        }
    }

    /**
     * @return mixed
     */
    public function getQuestionID()
    {
        return $this->questionID;
    }

    /**
     * @param mixed $questionID
     */
    public function setQuestionID($questionID)
    {
        if(isset($questionID) && !empty($questionID)) {
            $this->questionID = $questionID;
        }
    }

    /**
     * @return mixed
     */
    public function getResponseText()
    {
        return $this->responseText;
    }

    /**
     * @param mixed $responseText
     */
    public function setResponseText($responseText)
    {
        if(isset($responseText) && !empty($responseText)) {
            $this->responseText = $responseText;
        }
    }

    //Database Operations
    public function saveResponse($userID = null)
    {
        try
        {
            $dbh = DatabaseConnection::getInstance();

            if ($this->responseID == null)
            {
                $stmtHandle = $dbh->prepare("INSERT INTO `response` (questionID, applicationID, responseText) 
                                  VALUES (:questionID, :applicationID, :responseText)");
                $stmtHandle->bindValue("applicationID", $this ->applicationID);
                $stmtHandle->bindValue("questionID", $this ->questionID);
                $stmtHandle->bindValue("responseText", $this ->responseText);

                $stmtHandle->execute();

                //$stmtHandle = $dbh->prepare("SELECT LAST_INSERT_ID() FROM Application");
                $this->responseID = $dbh->lastInsertID();
            }
            else
            {
                //Get the application id for this response, based on the userID
                $stmtHandle = $dbh->prepare("SELECT T1.applicationID
                                              FROM application as T1
                                              INNER JOIN response as T2
                                              ON T1.applicationID = T2.applicationID
                                              WHERE T1.userID = :userID AND 
                                              T2.responseID = :responseID");
                $stmtHandle->bindValue("userID", $userID);
                $stmtHandle->bindValue("responseID", $this->responseID);
                $stmtHandle->execute();
                $stmtHandle->setFetchMode(\PDO::FETCH_ASSOC);
                $applicationID = $stmtHandle->Fetch();
                $applicationID = $applicationID['applicationID'];

                $stmtHandle = $dbh->prepare("UPDATE `response` 
                                            SET responseText = :responseText
                                            WHERE responseID = :responseID
                                            AND applicationID = :applicationID");
                $stmtHandle->bindValue("applicationID", $applicationID);
                $stmtHandle->bindValue("responseText", $this->responseText);
                $stmtHandle->bindValue("responseID", $this->responseID);

                $stmtHandle->execute();
            }
        }
        catch (\PDOException $e)
        {
            //print "Error!: " . $e->getMessage() . "</br>";
        }
    }

    /**
     * If $dbRow is null, it will query the database using the $this->responseID; Otherwise, it will set all properties from $dbRow
     * @param array|object|null $dbRow: An associative array or object containing the key/value pairs of an Response object's properties (i.e. a database row).
     * @return self $this
     */
    public function load($dbRow = null)
    {
        // Query the database if an array/object is not provided, but the applicationID is set.
        if (is_null($dbRow) && isset($this->applicationID))
        {
            try
            {
                $stmt = $this->dbh->prepare("SELECT * FROM `response` WHERE `responseID` = :responseID");
                $stmt->bindParam('responseID', $this->responseID);
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
        else
        {
            foreach ($dbRow as $property => $value)
            {
                if (method_exists($this, ($method = 'set' . ucfirst($property)) ))
                {
                    $this->$method($value);
                }
            }
        }

        return $this;
    }

    /**
     * Loads the object from the database.
     * @param array $args: an associative array with either a 'responseID' key/value pair or an 'applicationID' key/value pair.
     */
    static function loadResponses($args)
    {
        try {
            $dbh = DatabaseConnection::getInstance();
            if (isset($args['responseID'])) {
                $stmt = $dbh->prepare("SELECT * FROM `response` WHERE responseID = :responseID");
                $stmt->bindParam('responseID', $args['responseID']);
            } else if (isset($args['applicationID'])) {
                $stmt = $dbh->prepare("SELECT * FROM `response` WHERE applicationID = :applicationID");
                $stmt->bindParam('applicationID', $args['applicationID']);
            } else {
                $stmt = $dbh->prepare("SELECT * FROM `application`");
            }

            $stmt->execute();
            $stmt->setFetchMode(\PDO::FETCH_ASSOC);
            $data = $stmt->fetchAll();

            $responses = array();

            foreach ($data as $row) //$propertyName => $value)
            {
                $response = new Response();
                foreach ($row as $property => $value) {
                    if (method_exists($response, ($method = 'set' . ucfirst($property)))) {
                        $response->$method($value);
                    }
                }
                $responses[] = $response;
            }

            return $responses;
        }
        catch (\PDOException $e) {

        }
    }

    function jsonSerialize()
    {
        // TODO: Implement jsonSerialize() method.
        return
            array(
                "responseID"=> $this->getResponseID(),
                "questionID"=> $this->getQuestionID(),
                "applicationID"=> $this->getApplicationID(),
                "responseText"=> $this->getResponseText()
            );
    }
}