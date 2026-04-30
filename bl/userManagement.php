<?php

require_once "../model/database.php";
require_once "../model/registModel.php";

class UserManagement
{
    private $regsMode1;
    public function __construct()
    {
        $database = new Database();
        $db = $database->connect();
        $this->regsMode1 = new Registration($db);
    }

    public function getUser()
    {
        $response = $this->regsMode1->readRegistrations();
        return $response->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addUserFunc($fName, $lName, $uName, $email, $password)
    {
        try {
            if ($this->regsMode1->createRegistration($fName, $lName, $uName, $email, $password)) {
                echo "true";
            } else {
                echo "false";
            }
        } catch (Exception $ex) {
            http_response_code(500);
            echo $ex->getMessage();
            exit();
        }
    }

    public function updateUserFunc($fName, $lName, $uName, $email, $password, $userID)
    {
        try {
            if ($this->regsMode1->updateRegistration($fName, $lName, $uName, $email, $password, $userID)) {
                echo "true";
            } else {
                echo "false";
            }
        } catch (Exception $ex) {
            http_response_code(500);
            echo $ex->getMessage();
            exit();
        }
    }

    public function deleteUserFunc($userID)
    {
        try {
            if ($this->regsMode1->deleteRegistration($userID)) {
                echo "true";
            } else {
                echo "false";
            }
        } catch (Exception $ex) {
            http_response_code(500);
            echo $ex->getMessage();
            exit();
        }
    }

    public function loginFunc($luname, $lpass)
    {
        try {
            if ($this->regsMode1->loginRegistration($luname, $lpass)) {
                echo "true";
            } else {
                echo "false";
            }
        } catch (Exception $ex) {
            http_response_code(500);
            echo $ex->getMessage();
            exit();
        }
    }
}
