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

$currentDateTime = date('d-m-Y H:i');
$account = R::findOne('users', 'id = ?', array($_SESSION['id']));

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $varId = $_POST['var_id'];
    $taskIndex = $_POST['task_index'];
    $answer_given = trim($_POST['answer_given']);

    $variant = R::findOne('variants', 'id = ?', [$varId]);

    $var_data = explode("|", $variant->create_data);
    $usr_data = explode("|", $variant->user_data);

    $task_data_array = explode(":", $var_data[$taskIndex]);
    $t_num = $task_data_array[0];
    $t_id = $task_data_array[1];
    $t_ans = trim($task_data_array[2]);

    $newstr = "$t_num:$t_id:$answer_given";
    if (explode(":", $usr_data[$taskIndex])[2]) {
        exit();
    }

    $newLog = R::dispense("logs");
    $newLog->user_id = $account->id;
    $newLog->user_name = $account->name;
    $newLog->typeof = "task";
    $newLog->var_id = 0;
    $newLog->task_num = $t_num;
    $newLog->task_id = $t_id;
    $newLog->task_userans = $answer_given;
    $newLog->date = $currentDateTime;


    if ($t_num >= 1 and $t_num <=  25) {
        if ($answer_given == $t_ans) {
            $usr_data[$taskIndex] = $newstr;
            $usr_data_string = implode("|", $usr_data);

            $variant->user_data = $usr_data_string;
            echo 1;
            R::store($variant);
            R::store($newLog);
        }
        else {
            echo 0;
            $newLog->data = "answer_incorrect";
            R::store($newLog);
        }
    }

    else {
        $hard_answers_users = explode(" ", $answer_given);
        $hard_answers = explode(" ", $t_ans);

        if ($hard_answers_users[0] == $hard_answers[0] && $hard_answers_users[1] == $hard_answers[1]) {
            $usr_data[$taskIndex] = $newstr;
            $usr_data_string = implode("|", $usr_data);

            $variant->user_data = $usr_data_string;
            R::store($variant);
            R::store($newLog);
            echo 1;
        }
        else {
            if ($hard_answers_users[0] == $hard_answers[0]) {
                echo 2;
                $newLog->data = "answer_half_correct";
                R::store($newLog);
            }
            elseif ($hard_answers_users[1] == $hard_answers[1]) {
                echo 3;
                $newLog->data = "answer_half_correct";
                R::store($newLog);
            }
            else {
                echo 0;
                $newLog->data = "answer_incorrect";
                R::store($newLog);
            }
        }

    }
}
?>