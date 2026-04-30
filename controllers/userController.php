<?php

session_start();
require_once '../bl/userManagement.php';
require_once '../helper/sendEmail.php';

$userManagement = new UserManagement();

if (isset($_POST['fName'], $_POST['lName'], $_POST['uName'], $_POST['email'], $_POST['password'])) {
    $fName    = $_POST['fName'];
    $lName    = $_POST['lName'];
    $uName    = $_POST['uName'];
    $email    = $_POST['email'];
    $password = $_POST['password'];

    $userManagement->addUserFunc($fName, $lName, $uName, $email, $password);

    $output = ob_get_contents();
    if (strpos($output, 'true') !== false || $output === '') {
        $fullName = trim($fName . ' ' . $lName);
        sendWelcomeEmail($email, $fullName);
    }
    exit;
}

if (isset($_POST['ufName'], $_POST['ulName'], $_POST['uuName'], $_POST['uemail'], $_POST['upassword'], $_POST['uid'])) {
    $userManagement->updateUserFunc($_POST['ufName'], $_POST['ulName'], $_POST['uuName'], $_POST['uemail'], $_POST['upassword'], $_POST['uid']);
    exit;
}

if (isset($_POST['did'])) {
    $userManagement->deleteUserFunc($_POST['did']);
    exit;
}

if (isset($_POST['luname'], $_POST['lpass'])) {
    $userManagement->loginFunc($_POST['luname'], $_POST['lpass']);
    exit;
}
