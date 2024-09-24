<?php
include("../../../libs/db.php");
function generateSalt() {
    $salt = '';
    $saltLength = 15; //длина соли
    for($i=0; $i<$saltLength; $i++) {
        $salt .= chr(mt_rand(33,126)); //символ из ASCII-table
    }
    return $salt;
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $date = $_POST["date"];
    $login = $_POST["login"];
    $password = $_POST["password"];
    
    $newStudent = R::dispense("users");
    $newStudent->name = $name;
    $newStudent->date = $date;
    $newStudent->login = $login;
    $newStudent->password = password_hash($password, PASSWORD_DEFAULT);
    $currentDateTime = date('d-m-Y H:i');
    $newStudent->dateCreated = $currentDateTime;

    $newStudent->admin = 0;
    $newStudent->cookie = "";
    R::store($newStudent);
}

?>