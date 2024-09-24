<?php
include("../../../libs/db.php");

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


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $varId = $_POST['var_id'];

    $variant = R::findOne('variants', 'id = ?', [$varId]);
    if ($variant->for_user == $account->id) {

        $variant->user_data = "";
        R::store($variant);
    }

}