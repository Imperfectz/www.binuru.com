<?php

class Registration
{

    private $conn;
    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function emailExists($email)
    {
        $q = $this->conn->prepare("SELECT COUNT(*) FROM users_tbl WHERE email = :email");
        $q->bindParam(':email', $email);
        $q->execute();
        return (int)$q->fetchColumn() > 0;
    }

    public function usernameExists($uName, $excludeId = null)
    {
        $sql = "SELECT COUNT(*) FROM users_tbl WHERE userName = :uName";
        if ($excludeId) $sql .= " AND userID != :excludeId";
        $q = $this->conn->prepare($sql);
        $q->bindParam(':uName', $uName);
        if ($excludeId) $q->bindParam(':excludeId', $excludeId, PDO::PARAM_INT);
        $q->execute();
        return (int)$q->fetchColumn() > 0;
    }

    public function createRegistration($fname, $lname, $uName, $email, $password)
    {
        $query = "INSERT INTO users_tbl
        (firstName, lastName, userName, email, createdAt, updatedAt, password) 
        VALUES (:firstName, :lastName, :userName, :email, :createdAt, :updatedAt, :password)";
        $response = $this->conn->prepare($query);

        $password_hash = password_hash($password, PASSWORD_ARGON2ID);
        $dateNow = date('Y-m-d H:i:s');

        $response->bindParam(':firstName', $fname);
        $response->bindParam(':lastName',  $lname);
        $response->bindParam(':userName',  $uName);
        $response->bindParam(':email',     $email);
        $response->bindParam(':password',  $password_hash);
        $response->bindParam(':createdAt', $dateNow);
        $response->bindParam(':updatedAt', $dateNow);

        return $response->execute();
    }

    public function readRegistrations()
    {
        $query = "SELECT * FROM users_tbl";
        $response = $this->conn->prepare($query);
        $response->execute();
        return $response;
    }

    public function updateRegistration($fname, $lname, $uName, $email, $password, $userID)
    {
        $dateNow = date('Y-m-d H:i:s');

        if ($password) {
            // New password provided — update it
            $query = "UPDATE users_tbl 
                      SET firstName = :firstName,
                          lastName  = :lastName,
                          userName  = :userName,
                          email     = :email,
                          password  = :password,
                          updatedAt = :updatedAt
                      WHERE userID = :userID";
            $response = $this->conn->prepare($query);
            $password_hash = password_hash($password, PASSWORD_ARGON2ID);
            $response->bindParam(':password', $password_hash);
        } else {
            // No new password — leave it untouched
            $query = "UPDATE users_tbl 
                      SET firstName = :firstName,
                          lastName  = :lastName,
                          userName  = :userName,
                          email     = :email,
                          updatedAt = :updatedAt
                      WHERE userID = :userID";
            $response = $this->conn->prepare($query);
        }

        $response->bindParam(':userID',    $userID);
        $response->bindParam(':firstName', $fname);
        $response->bindParam(':lastName',  $lname);
        $response->bindParam(':userName',  $uName);
        $response->bindParam(':email',     $email);
        $response->bindParam(':updatedAt', $dateNow);

        return $response->execute();
    }

    public function deleteRegistration($userID)
    {
        $query = "DELETE FROM users_tbl WHERE userID = :userID";
        $response = $this->conn->prepare($query);
        $response->bindParam(':userID', $userID);
        return $response->execute();
    }

    public function loginRegistration($email, $password)
    {
        $query = "SELECT userID, firstName, lastName, userName, email, password, is_admin FROM users_tbl WHERE email = :email LIMIT 1";
        $response = $this->conn->prepare($query);
        $response->bindParam(':email', $email);
        $response->execute();

        $user = $response->fetch(PDO::FETCH_ASSOC);
        if (!$user) {
            return false;
        }

        if (!password_verify($password, $user['password'])) {
            return false;
        }

        return $user;
    }
}
