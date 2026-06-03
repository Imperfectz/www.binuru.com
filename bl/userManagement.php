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
            if ($this->regsMode1->emailExists($email)) {
                echo "Email is already registered.";
                return;
            }
            if ($this->regsMode1->usernameExists($uName)) {
                echo "Username is already taken.";
                return;
            }
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

    public function loginFunc($email, $password)
    {
        try {
            $user = $this->regsMode1->loginRegistration($email, $password);

            if ($user) {
                $_SESSION['userID']    = $user['userID'];
                $_SESSION['userName']  = $user['userName'];
                $_SESSION['email']     = $user['email'];
                $_SESSION['firstName'] = $user['firstName'];
                $_SESSION['lastName']  = $user['lastName'];
                $_SESSION['is_admin']  = (bool)$user['is_admin'];
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
