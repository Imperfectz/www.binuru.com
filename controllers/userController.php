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

    ob_start();
    $userManagement->addUserFunc($fName, $lName, $uName, $email, $password);
    $output = ob_get_clean();
    echo $output;

    if (trim($output) === 'true') {
        $fullName = trim($fName . ' ' . $lName);
        sendWelcomeEmail($email, $fullName);
    }
    exit;
}

if (isset($_POST['ufName'], $_POST['ulName'], $_POST['uuName'], $_POST['uemail'], $_POST['uid'])) {
    // Password is optional: only update it if a new plaintext value was sent
    $password = isset($_POST['upassword']) && $_POST['upassword'] !== '' ? $_POST['upassword'] : null;
    $userManagement->updateUserFunc($_POST['ufName'], $_POST['ulName'], $_POST['uuName'], $_POST['uemail'], $password, $_POST['uid']);
    exit;
}

if (isset($_POST['did'])) {
    $userManagement->deleteUserFunc($_POST['did']);
    exit;
}

if (isset($_POST['lemail'], $_POST['lpass'])) {
    $userManagement->loginFunc($_POST['lemail'], $_POST['lpass']);
    exit;
}
