<?php
require "libs/db.php";

if (empty($_SESSION['auth']) or $_SESSION['auth'] == false) {
    if ( !empty($_COOKIE['login']) and !empty($_COOKIE['key']) ) {
        $login = $_COOKIE['login'];
        $id = $_COOKIE['id'];
        $key = $_COOKIE['key'];

        $user = R::findOne('users', 'id = ?', array($id));

        if (in_array($key, explode("SEPARATE", $user->cookie))) {
          $account = $user;
        }

        if (!empty($account)) {
            session_start();
            $_SESSION['auth'] = true;
            $_SESSION['id'] = $account['id'];
            $_SESSION['login'] = $account['login'];
        }
    }
}
$account = R::findOne('users', 'id = ?', array($_SESSION['id']));
$account_firstname = explode(' ', $account->name)[0];

if ($account) {
    exit('<script>
    setTimeout(() => { window.location.replace("/informatics"); }, 10);
    </script>');
}

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="informatics/informatics_style/per_main.css">
    <link rel="stylesheet" href="informatics/informatics_style/login_page.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

	<link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="/favicon/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/favicon/favicon-16x16.png">
	<link rel="manifest" href="/favicon/site.webmanifest">

	<link rel="mask-icon" href="/favicon/safari-pinned-tab.svg" color="#5bbad5">
	<meta name="msapplication-TileColor" content="#da532c">

    <script src="informatics/informatics_scripts/js/auth_student.js"></script>
    <script src="informatics/informatics_scripts/js/alerts.js"></script>
	<meta name="theme-color" content="#000000">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">


    <title>Авторизация</title>
</head>
<body>
<div id="login_output"></div>
<main id="login_page">

    <div id="login_form">
        <h1 class="login_form_header">Авторизация</h1>
        <div class="login_form_input_block">
            <span class="login_form_input_desc"><i class="fa fa-user login_input_icon" aria-hidden="true"></i> Введите Ваш логин:</span>
            <input id="login_form_login" class="login_form_input" type="text" placeholder="Логин">
        </div>

        <div class="login_form_input_block">
            <span class="login_form_input_desc"><i class="fa fa-lock login_input_icon" aria-hidden="true"></i> Введите Ваш пароль:</span>
            <input id="login_form_password" class="login_form_input" type="password" placeholder="Пароль">
        </div>

        <div class="login_form_input_block">
            <button id="login_submit">Войти</button>
        </div>


    </div>

</main>

</body>
</html>