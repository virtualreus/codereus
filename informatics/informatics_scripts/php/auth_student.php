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
function generateSalt() {
    $salt = '';
    $saltLength = 15; //длина соли
    for($i=0; $i<$saltLength; $i++) {
        $salt .= chr(mt_rand(33,126)); //символ из ASCII-table
    }
    return $salt;
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = $_POST["login"];
    $password = $_POST["password"];
    $account = R::findOne('users', 'login = ?' , [$login]);

    if (!$account) {
        # 1 - ошибка, которая гласит о несуществующем аккаунте
        exit("1");
    }

    if (!password_verify($password, $account->password)) {
        # 2 - ошибка, которая гласит о неверном пароле для существующего аккаунта.
        exit("2");
    }

    if (password_verify($password, $account->password)) {
        $_SESSION['auth'] = true;
        $_SESSION['id'] = $user->id;
        $_SESSION['login'] = $user->login;

        $key = generateSalt();
        setcookie('id', $account->id, time()+60*60*24*30, '/');
        setcookie('login', $account->login, time()+60*60*24*30, '/');
        setcookie('key', $key, time()+60*60*24*30, '/');
        $cookies = explode("SEPARATE", $account->cookie);

        array_push($cookies, $key);
        $account->cookie = implode("SEPARATE", $cookies);
        R::store($account);
        exit('<script>
        setTimeout(() => { window.location.replace("../informatics"); }, 200);
        </script>');
    }
}

?>