<?php
/**
 * Created by PhpStorm.
 * User: kolefrazier
 * Date: 10/31/2017
 * Time: 4:01 PM
 */

namespace Scholarship\Models;
use \Scholarship\Utilities\DatabaseConnection;


class User implements \JsonSerializable
{
    // CONSTANT
    const ROLE_STUDENT = "Student";
    const ROLE_FACULTY = "Faculty";

    //Attributes
    private $wNumber;
    private $firstName;
    private $lastName;
    private $middleInitial;
    private $birthDate;
    private $address;
    private $secondAddress;
    private $city;
    private $state;
    private $zipCode;
    private $gender;
    private $maritalStatus;
    private $homePhone;
    private $cellPhone;
    private $role;
    private $username;
    private $citizenship;

    public function __construct()
    {

    }

    function jsonSerialize()
    {
        $rtn = array(
            'wNumber' => $this->wNumber,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'middleInitial' => $this->middleInitial,
            'birthDate' => $this->birthDate,
            'address' => $this->address,
            'secondAddress' => $this->secondAddress,
            'city' => $this->city,
            'state' => $this->state,
            'zipCode' => $this->zipCode,
            'gender' => $this->gender,
            'maritalStatus' => $this->maritalStatus,
            'homePhone' => $this->homePhone,
            'cellPhone' => $this->cellPhone,
            'role' => $this->role
        );
        return $rtn;
    }

    //Getters and Setters
    /**
     * @return mixed
     */
    public function getWNumber()
    {
        return $this->wNumber;
    }

    /**
     * @param mixed $wNumber
     */
    public function setWNumber($wNumber)
    {
        $this->wNumber = $wNumber;
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param mixed $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param mixed $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return mixed
     */
    public function getMiddleInitial()
    {
        return $this->middleInitial;
    }

    /**
     * @param mixed $middleInitial
     */
    public function setMiddleInitial($middleInitial)
    {
        $this->middleInitial = $middleInitial;
    }

    /**
     * @return mixed
     */
    public function getBirthDate()
    {
        return $this->birthDate;
    }

    /**
     * @param mixed $birthDate
     */
    public function setBirthDate($birthDate)
    {
        $this->birthDate = $birthDate;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param mixed $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return mixed
     */
    public function getSecondAddress()
    {
        return $this->secondAddress;
    }

    /**
     * @param mixed $secondAddress
     */
    public function setSecondAddress($secondAddress)
    {
        $this->secondAddress = $secondAddress;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param mixed $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param mixed $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * @return mixed
     */
    public function getZipCode()
    {
        return $this->zipCode;
    }

    /**
     * @param mixed $zipCode
     */
    public function setZipCode($zipCode)
    {
        $this->zipCode = $zipCode;
    }

    /**
     * @return mixed
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @param mixed $gender
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
    }

    /**
     * @return mixed
     */
    public function getMaritalStatus()
    {
        return $this->maritalStatus;
    }

    /**
     * @param mixed $maritalStatus
     */
    public function setMaritalStatus($maritalStatus)
    {
        $this->maritalStatus = $maritalStatus;
    }

    /**
     * @return mixed
     */
    public function getHomePhone()
    {
        return $this->homePhone;
    }

    /**
     * @param mixed $homePhone
     */
    public function setHomePhone($homePhone)
    {
        $this->homePhone = $homePhone;
    }

    /**
     * @return mixed
     */
    public function getCellPhone()
    {
        return $this->cellPhone;
    }

    /**
     * @param mixed $cellPhone
     */
    public function setCellPhone($cellPhone)
    {
        $this->cellPhone = $cellPhone;
    }

    /**
     * @return mixed
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param mixed $role
     */
    public function setRole($role)
    {
        $this->role = $role;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getCitizenship()
    {
        return $this->citizenship;
    }

    /**
     * @param mixed $citizenship
     */
    public function setCitizenship($citizenship)
    {
        $this->citizenship = $citizenship;
    }

    //Database Methods

    /*
     * This method creates a user in the user table
     */
    public function create()
    {
        try
        {
            $dbh = DatabaseConnection::getInstance();
            $stmtHandle = $dbh->prepare(
                "INSERT INTO `User`(
                `wNumber`, 
                `firstName`, 
                `middleInitial`, 
                `lastName`, 
                `gender`, 
                `birthDate`, 
                `maritalStatus`, 
                `address`, 
                `secondAddress`, 
                `city`, 
                `state`, 
                `zip`, 
                `homePhone`, 
                `cellPhone`, 
                `role`, 
                `username`,
                `citizenship`) 
                VALUES (:wNumber, :firstName, :middleInitial, :lastName, :gender, :birthDate,
                :maritalStatus,:address,:secondAddress,:city,:state,:zip,
                :homePhone,:cellPhone,:role,:username, :citizenship)");

            $stmtHandle->bindValue(":wNumber", $this->wNumber);
            $stmtHandle->bindValue(":firstName", $this->firstName);
            $stmtHandle->bindValue(":middleInitial", $this->middleInitial);
            $stmtHandle->bindValue(":lastName", $this->lastName);
            $stmtHandle->bindValue(":gender", $this->gender);
            $stmtHandle->bindValue(":birthDate", $this->birthDate);
            $stmtHandle->bindValue(":maritalStatus", $this->maritalStatus);
            $stmtHandle->bindValue(":address", $this->address);
            $stmtHandle->bindValue(":secondAddress", $this->secondAddress);
            $stmtHandle->bindValue(":city", $this->city);
            $stmtHandle->bindValue(":state", $this->state);
            $stmtHandle->bindValue(":zip", $this->zipCode);
            $stmtHandle->bindValue(":homePhone", $this->homePhone);
            $stmtHandle->bindValue(":cellPhone", $this->cellPhone);
            $stmtHandle->bindValue(":role", $this->role);
            $stmtHandle->bindValue(":username", $this->username);
            $stmtHandle->bindValue(":citizenship", $this->citizenship);


            $success = $stmtHandle->execute();

            if (!$success)
            {
                throw new \PDOException("sql query execution failed");
            }

        }
        catch (\Exception $e)
        {
            throw $e;
        }
    }

    /*
     * This method updates the corresponding user in the database based on the data this user object holds
     */
    public function update()
    {
        try
        {
            if (empty($this->wNumber))
            {
                die("error: the wnumber is not provided");
            }
            else
            {
                $dbh = DatabaseConnection::getInstance();
                $stmtHandle = $dbh->prepare(
                    "UPDATE `User` 
             SET `firstName`= :firstName,
                 `middleInitial`= :middleInitial,
                 `lastName`= :lastName,
                 `gender`= :gender,
                 `birthDate`= :birthDate,
                 `maritalStatus`= :maritalStatus,
                 `address`= :address,
                 `secondAddress`= :secondAddress,
                 `city`= :city,
                 `state`= :state,
                 `zip`= :zip,
                 `homePhone`= :homePhone,
                 `cellPhone`= :cellPhone,
                 `citizenship`= :citizenship
             WHERE `wNumber` = :wNumber");

                $stmtHandle->bindValue(":wNumber", $this->wNumber);
                $stmtHandle->bindValue(":firstName", $this->firstName);
                $stmtHandle->bindValue(":middleInitial", $this->middleInitial);
                $stmtHandle->bindValue(":lastName", $this->lastName);
                $stmtHandle->bindValue(":gender", $this->gender);
                $stmtHandle->bindValue(":birthDate", $this->birthDate);
                $stmtHandle->bindValue(":maritalStatus", $this->maritalStatus);
                $stmtHandle->bindValue(":address", $this->address);
                $stmtHandle->bindValue(":secondAddress", $this->secondAddress);
                $stmtHandle->bindValue(":city", $this->city);
                $stmtHandle->bindValue(":state", $this->state);
                $stmtHandle->bindValue(":zip", $this->zipCode);
                $stmtHandle->bindValue(":homePhone", $this->homePhone);
                $stmtHandle->bindValue(":cellPhone", $this->cellPhone);
                $stmtHandle->bindValue(":citizenship", $this->citizenship);


                $success = $stmtHandle->execute();

                if (!$success) {
                    throw new \PDOException("user full update operation failed.");
                }
            }
        }
        catch (\PDOException $e)
        {
            throw $e;
        }
    }

    public function delete()
    {
        try
        {
            if (empty($this->wNumber))
            {
                die("error: the wnumber is not provided");
            }
            else
            {
                $dbh = DatabaseConnection::getInstance();
                $stmtHandle = $dbh->prepare("DELETE FROM `User` WHERE `wNumber` = :wNumber");
                $stmtHandle->bindValue(":wNumber", $this->getWNumber());
                $success = $stmtHandle->execute();

                if (!$success) {
                    throw new \PDOException("user full update operation failed.");
                }
            }
        }
        catch (\PDOException $e)
        {
            throw $e;
        }
    }

    /*
     * This method loads a user object with data from the database by the user object's wNumber
     */
    public function load()
    {
        try
        {
            if (empty($this->wNumber))
            {
                die("error: the wnumber is not provided");
            }
            else
            {
                $dbh = DatabaseConnection::getInstance();
                $stmtHandle = $dbh->prepare("SELECT * FROM `User` WHERE wNumber = :wNumber");
                $stmtHandle->bindValue(":wNumber", $this->wNumber);

                $stmtHandle->setFetchMode(\PDO::FETCH_ASSOC);
                $success = $stmtHandle->execute();

                if ($success === false) {
                    throw new \PDOException("error: fail to execute sql squery");
                }
                else
                {

                    $user = $stmtHandle->fetch();

                    if ($this->userExists())
                    {
                        $this->setFirstName($user['firstName']);
                        $this->setLastName($user['lastName']);
                        $this->setMiddleInitial($user['middleInitial']);
                        $this->setGender($user['gender']);
                        $this->setBirthDate($user['birthDate']);
                        $this->setMaritalStatus($user['maritalStatus']);
                        $this->setAddress($user['address']);
                        $this->setSecondAddress($user['secondAddress']);
                        $this->setCity($user['city']);
                        $this->setState($user['state']);
                        $this->setZipCode($user['zip']);
                        $this->setHomePhone($user['homePhone']);
                        $this->setCellPhone($user['cellPhone']);
                        $this->setRole($user['role'] == "Student" ? self::ROLE_STUDENT : self::ROLE_FACULTY);
                        $this->setUsername($user['username']);
                        $this->setCitizenship($user['citizenship']);
                    }
                    else
                    {
                        throw new \PDOException("error: this user does not exist in the database");
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
     * This method checks if the user exists in the database based on the wNumber that is set on the User object
     * return true if the user with this wNumber exists otherwise false
     */
    public function userExists() : bool
    {
        try
        {
            $dbh = DatabaseConnection::getInstance();
            $stmtHandle = $dbh->prepare("SELECT * FROM `User` WHERE wNumber = :wNumber");
            $stmtHandle->bindValue(":wNumber", $this->wNumber);

            $stmtHandle->setFetchMode(\PDO::FETCH_ASSOC);

            $success = $stmtHandle->execute();

            if (!$success)
            {
                throw new \PDOException("error: fail to execute sql query");
            }
            else
            {
                return ($stmtHandle->rowCount() != 0 ? true : false);
            }
        }
        catch (\Exception $e)
        {
            throw $e;
        }
    }

    public function userExistsByUsername() : bool
    {
        try
        {
            $dbh = DatabaseConnection::getInstance();
            $stmtHandle = $dbh->prepare("SELECT * FROM `User` WHERE username = :username");
            $stmtHandle->bindValue(":username", $this->username);

            $stmtHandle->setFetchMode(\PDO::FETCH_ASSOC);

            $success = $stmtHandle->execute();

            if (!$success)
            {
                throw new \PDOException("error: fail to execute sql query");
            }
            else
            {
                return ($stmtHandle->rowCount() != 0 ? true : false);
            }
        }
        catch (\Exception $e)
        {
            throw $e;
        }
    }

    /*
     * This method returns the wNumber for the user based on the username that is passed in
     * return the wNumber if there is a user with the username found otherwise null
     */
    public function getIDByUsername($username)
    {
        try
        {
            $dbh = DatabaseConnection::getInstance();
            $stmtHandle = $dbh->prepare("SELECT wNumber FROM `User` WHERE username = :username");
            $stmtHandle->bindValue(":username", $username);

            $stmtHandle->setFetchMode(\PDO::FETCH_ASSOC);
            $success = $stmtHandle->execute();

            if (!$success)
            {
                throw new \PDOException("error: fail to execute sql query");
            }
            else
            {
                if ($stmtHandle->rowCount() != 0)
                {
                    $user = $stmtHandle->fetch();
                    return $user['wNumber'];
                }
                else
                {
                    throw new \PDOException("error: this user does not exist in the database");
                }

            }

        }
        catch (\Exception $e)
        {
            throw $e;
        }

    }

}