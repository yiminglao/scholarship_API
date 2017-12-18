<?php
/**
 * Created by PhpStorm.
 * User: Justus Brown
 * Date: 11/1/2017
 * Time: 8:21 AM
 */

namespace Scholarship\Models;

use Scholarship\Utilities\DatabaseConnection;

class TimeFrame
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = DatabaseConnection::getInstance();
    }

    //Query the database for the id and return the start and end date
    public function getDate(int $id) {
        $stmt = $this->pdo->prepare("SELECT * FROM timeframe WHERE timeframeID = :id");
        $stmt->bindParam(":id", $id);
        $stmt->setFetchMode(\PDO::FETCH_ASSOC);
        $stmt->execute();
        $result = $stmt->fetchAll();
        if(!empty($result)) {
            return $result;
        }
    }

    //Query the database for all the dates
    public function getAllDates() {
        $stmt = $this->pdo->prepare("SELECT * FROM timeframe");
        $stmt->setFetchMode(\PDO::FETCH_ASSOC);
        $stmt->execute();
        $result = $stmt->fetchAll();
        if (!empty($result)) {
            return $result;
        } else {
            return -1;
        }
    }

    //Query the database for the id. If it is there then update the start and end date
    public function updateDate(int $id, string $startDate, string $endDate) {
        $stmt = $this->pdo->prepare("UPDATE timeframe SET startDate = :startDate, endDate = :endDate WHERE timeframeID = :id");
        $stmt->bindParam(":startDate", $startDate);
        $stmt->bindParam(":endDate", $endDate);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
    }

    //What would this return if there was no idea for that record?
    //Query the database for the id. If it is there then update the start date
    public function updateStartDate(int $id, string $startDate) {
        $stmt = $this->pdo->prepare("UPDATE timeframe SET startDate = :startDate WHERE timeframeID = :id");
        $stmt->bindParam(":startDate", $startDate);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt;
    }

    //Query the database for the id. If it is there then update the end date
    public function updateEndDate(int $id, string $endDate) {
        $stmt = $this->pdo->prepare("UPDATE timeframe SET endDate = :endDate WHERE timeFrameID = :id");
        $stmt->bindParam(":endDate", $endDate);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
    }

    //Query the database for the startDate and endDate. If a record doesn't exist with those start and end dates then create a new one
    public function createTimeFrame(string $startDate, string $endDate) {
        $stmt = $this->pdo->prepare("INSERT INTO timeframe (startDate, endDate) VALUES (:startDate, :endDate)");
        $stmt->bindParam(":startDate", $startDate);
        $stmt->bindParam(":endDate", $endDate);
        $stmt->execute();
    }

    //How would you check the sql statement to see if the id is actually there before doing the datecheck?
    //Query the database for the id. If it is there then check if checkDate is within the start and end date
    public function isWithinTimeFrame(int $id) {
        $curDate = date('Y-m-d');
        $stmt = $this->pdo->prepare("SELECT * FROM timeframe WHERE startDate <= :curdate AND endDate >= :curdate AND timeFrameID = :id");
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":curdate", $curDate);
        $stmt->setFetchMode(\PDO::FETCH_NUM);
        $stmt->execute();
        $result = $stmt->fetchAll();
        if (!empty($result)) {
            return true;
        } else {
            return false;
        }
    }

    //Query the database for the id. If it is there then delete it
    public function deleteTimeFrame(int $id) {
        $stmt = $this->pdo->prepare("DELETE FROM timeframe WHERE timeframeID = :id");
        $stmt->bindParam("id", $id);
        $stmt->execute();
    }
}