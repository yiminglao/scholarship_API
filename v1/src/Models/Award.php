<?php
/**
 * Created by PhpStorm.
 * User: Kelson
 * Date: 11/1/2017
 * Time: 8:32 AM
 */

namespace Scholarship\Models;

use Scholarship\Utilities\DatabaseConnection;

//define('__ROOT__', dirname(dirname(__FILE__)));
require_once(__ROOT__.'/Utilities/DatabaseConnection.php');

class Award implements \JsonSerializable
{
    const ACCEPTED = "accepted";
    const DECLINED = "declined";
    public $id;
    public $scholarshipId;
    public $userId;
    public $timeframeId;
    public $awardAmount;
    public $decisionDate;
    public $decision;

    /**
     * Award constructor.
     * @param $scholarshipId
     * @param $userId
     * @param $timeframeId
     */
    public function __construct()
    {

    }

    function jsonSerialize()
    {
        return
            array(
                "id" => $this->id,
                "scholarshipId"=> $this->scholarshipId,
                "userId"=>$this->userId,
                "timeframeId"=>$this->timeframeId,
                "decisionDate"=>$this->decisionDate,
                "decision"=>$this->decision
            );
    }

    public function init(int $scholarshipId, int $userId, int $timeframeId, int $awardAmount, string $decisionDate = null, string $decision = null){
        $this->scholarshipId = $scholarshipId;
        $this->userId = $userId;
        $this->timeframeId = $timeframeId;
        $this->awardAmount = $awardAmount;
        $this->decisionDate = $decisionDate;
        $this->decision = $decision;

        $dbh = DatabaseConnection::getInstance();
        $stmtCreate = $dbh->prepare("INSERT INTO `scholarship_dev`.`award`(scholarshipId,userId,timeframeId,awardAmount,decisionDate,decision)
        VALUES(:scholarshipId, :userId, :timeframeId, :awardAmount, :decisionDate, :decision)");



        $stmtCreate->bindParam(":scholarshipId", $scholarshipId);
        $stmtCreate->bindParam(":userId", $userId);
        $stmtCreate->bindParam(":timeframeId", $timeframeId);
        $stmtCreate->bindParam(":awardAmount", $awardAmount);
        $stmtCreate->bindParam(":decisionDate", $decisionDate);
        $stmtCreate->bindParam(":decision", $decision);

        $stmtCreate->execute();
        //TODO test this
        $this->id = $dbh->lastInsertId('id');
    }

    public function update(){
        if ($this->id === 0){
            throw new Exception("No id ");
        }
        try {
            $dbh = DatabaseConnection::getInstance();
            $stmtUpdate = $dbh->prepare("UPDATE `scholarship_dev`.`award` SET 
            scholarshipId = :scholarshipId,
            userId = :userId,
            timeframeId = :timeframeId,
            awardAmount = :awardAmount,
            decisionDate = :decisionDate,
            decision = :decision
            WHERE id = :id");

            $stmtUpdate->bindParam(":scholarshipId", $this->scholarshipId);
            $stmtUpdate->bindParam(":userId", $this->userId);
            $stmtUpdate->bindParam(":timeframeId", $this->timeframeId);
            $stmtUpdate->bindParam(":awardAmount", $this->awardAmount);
            $stmtUpdate->bindParam(":decisionDate", $this->decisionDate);
            $stmtUpdate->bindParam(":decision", $this->decision);
            $stmtUpdate->bindParam(":id", $this->id);

            $stmtUpdate->execute();
        }
        catch(Exception $e){
            throw $e;
        }
    }

    public function select(){

        //$award = new Award();
        //$award->id = 1
        //$award->select()
        //return $award->jsonify()
        if ($this->id === 0){
            throw new Exception("No id ");
        }
        try {
            $dbh = DatabaseConnection::getInstance();
            $stmtSelect = $dbh->prepare("SELECT * FROM `scholarship_dev`.`award` WHERE id = :id");
            $stmtSelect->bindValue("id", $this->id);

            $stmtSelect->setFetchMode(\PDO::FETCH_ASSOC);
            $stmtSelect->execute();
            $val = $stmtSelect->fetch();

//        public $scholarshipId;
//        public $userId;
//        public $timeframeId;
//        public $awardAmount;
//        public $decisionDate;
//        public $decision;

            $this->scholarshipId = $val["scholarshipId"];
            $this->userId = $val["userId"];
            $this->timeframeId = $val['timeframeId'];
            $this->awardAmount = $val['awardAmount'];
            $this->decisionDate = $val['decisionDate'];
            $this->decision = $val['decision'];
        }
        catch(Exception $e){
            throw $e;
        }
    }



////POST function below
//    public static function createAward(int $scholarshipId, int $userId, int $timeframeId, int $awardAmount, string $decisionDate, string $decision){
//        $dbh = DatabaseConnection::getInstance();
//
//
//        $stmtCreate = $dbh->prepare("INSERT INTO `scholarship_dev`.`award`(scholarshipId,userId,timeframeId,awardAmount,decisionDate,decision)
//        VALUES(:scholarshipId, :userId, :timeframeId, :awardAmount, :decisionDate, :decision)");
//
//
//
//        $stmtCreate->bindParam(":scholarshipId", $scholarshipId);
//        $stmtCreate->bindParam(":userId", $userId);
//        $stmtCreate->bindParam(":timeframeId", $timeframeId);
//        $stmtCreate->bindParam(":awardAmount", $awardAmount);
//        $stmtCreate->bindParam(":decisionDate", $decisionDate);
//        $stmtCreate->bindParam(":decision", $decision);
//
//        $stmtCreate->execute();
//
//
//    }




//PUT function below
    public function updateAward(int $id, int $scholarshipId, int $userId, int $timeframeId, int $awardAmount, string $decisionDate, string $decision){
        $dbh = DatabaseConnection::getInstance();

        $stmtUpdate = $dbh->prepare("UPDATE `scholarship_dev`.`award` SET 
            scholarshipId = :scholarshipId,
            userId = :userId,
            timeframeId = :timeframeId,
            awardAmount = :awardAmount,
            decisionDate = :decisionDate,
            decision = :decision
            WHERE id = :id");



        $stmtUpdate->bindParam(":scholarshipId", $scholarshipId);
        $stmtUpdate->bindParam(":userId", $userId);
        $stmtUpdate->bindParam(":timeframeId", $timeframeId);
        $stmtUpdate->bindParam(":awardAmount", $awardAmount);
        $stmtUpdate->bindParam(":decisionDate", $decisionDate);
        $stmtUpdate->bindParam(":decision", $decision);
        $stmtUpdate->bindParam(":id", $id);

        $stmtUpdate->execute();
    }


    public static function getAllAwardsByUserID($userId){
        $dbh = DatabaseConnection::getInstance();
        $stmtGetAll = $dbh->prepare("Select * From `scholarship_dev`.`award` 
                                     WHERE userId = :userId");
        $stmtGetAll->bindParam(":userId", $userId);
        $stmtGetAll->execute();
        $returnVals = $stmtGetAll->FetchAll(\PDO::FETCH_CLASS, 'Scholarship\Models\Award');
        return $returnVals;
    }

    public function getAwardByAwardId($id){
        $dbh = DatabaseConnection::getInstance();
        $stmtGetAll = $dbh->prepare("Select * From `scholarship_dev`.`award` 
                                     WHERE id = :id");
        $stmtGetAll->bindParam(":id", $id);
        $stmtGetAll->execute();
        $returnVals = $stmtGetAll->FetchAll(\PDO::FETCH_CLASS, "Scholarship\Models\Award");
        return $returnVals;
    }

    //Sets
    public function setAwardDecision($id, $decision){
        $dbh = DatabaseConnection::getInstance();

        $stmtSet = $dbh->prepare("UPDATE `scholarship_dev`.`award` 
                                     SET decision = :decision
                                     WHERE id = :id");
        $stmtSet->bindParam(":decision", $decision);
        $stmtSet->bindParam(":id", $id);
        $stmtSet->execute();
    }

    public static function getAwardByScholarshipID($scholarshipID) {

        $dbh = DatabaseConnection::getInstance();
        $getAllAwards = ' SELECT * FROM award WHERE scholarshipId = :scholarshipID';
        $stmt = $dbh->prepare($getAllAwards);
        $stmt->bindValue("scholarshipID", $scholarshipID);
        $stmt->execute();
        $returnVals = $stmt->FetchAll(\PDO::FETCH_CLASS, "Scholarship\Models\Award");
        return $returnVals;
    }

    public static function getAllAwards() {
        $dbh = DatabaseConnection::getInstance();
        $getAllAwards = ' SELECT * FROM award';
        $stmt = $dbh->prepare($getAllAwards);
        $stmt->execute();

        $returnVals = $stmt->FetchAll(\PDO::FETCH_ASSOC);
        return $returnVals;
    }

    public function setDecision(string $decision){
        $this->decision = $decision;
    }
}