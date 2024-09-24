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
    $userAnswer = trim($_POST['answer']);
    $taskNumber = $_POST['task'];
    $taskId = $_POST['task_id'];
    
    $tableName = 'task' . $taskNumber;
    $task = R::load($tableName, $taskId);

    $currentDateTime = date('d-m-Y H:i');

    if (!$userAnswer) {
        exit('empty');
    }

    if ($task->id) {
        $newLog = R::dispense('logs');
        $newLog->user_id = $account->id;
        $newLog->user_name = $account->name;
        $newLog->typeof = "task";
        $newLog->var_id = 0;
        $newLog->task_num = $taskNumber;
        $newLog->task_id = $taskId;
        $newLog->task_userans = $userAnswer;
        $newLog->date = $currentDateTime;


        if ($taskNumber == 26 || $taskNumber == 27) {
            $hard_answers_users = explode(" ", $userAnswer);
            $hard_answers = explode(" ", $task->answers);

            if ($hard_answers_users[0] == $hard_answers[0] && $hard_answers_users[1] == $hard_answers[1]) {
                echo "correct";
                R::store($newLog);
            } 
            else {
                if ($hard_answers_users[0] == $hard_answers[0]) {
                    $newLog->data = "answer_half_correct";
                    echo "correct_A";
                }
                elseif ($hard_answers_users[1] == $hard_answers[1]) {
                    $newLog->data = "answer_half_correct";
                    echo "correct_B";
                }
                else {
                    echo "incorrect";
                }
            }
        } 
        else {
            if ($task->answers == $userAnswer) {
                $newLog->data = "answer_correct";
                echo "correct";
            }
            else {
                $newLog->data = "answer_incorrect";
                echo "incorrect";
            }
        }

        R::store($newLog);
        exit();
    }

}
?>