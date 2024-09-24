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
    $userAnswers = trim($_POST['answers']);
    $varId = $_POST['var_id'];

    $variant = R::findOne('variants', 'id = ?', [$varId]);
    if ($variant->for_user == $account->id) {

        $userAnswers_array = explode("|", $userAnswers);
        $user_array = [];
        foreach ($userAnswers_array as $user_element) {
            $user_parts = explode(':', $user_element);
            
            if (count($user_parts) === 2) {
                $user_array[] = [$user_parts[0], $user_parts[1]];
            }
        }

        $baseAnswers_array = explode("|", $variant->create_data);
        $base_array = [];
        foreach ($baseAnswers_array as $base_element) {
            $base_parts = explode(':', $base_element);
            
            if (count($base_parts) === 3) {
                $base_array[] = [$base_parts[0], $base_parts[1], $base_parts[2]];
            }
        }
        $variant->user_data = $userAnswers;
        R::store($variant);

        $log_string = "";
        $currentDateTime = date('d-m-Y H:i');

       for ($i = 0; $i < count($base_array); $i++) {
            $currVarBase = $base_array[$i];
            $currVarUser = $user_array[$i];

            $baseTaskNumber = $currVarBase[0];
            $baseTaskId = $currVarBase[1];
            $baseTaskAnswer = $currVarBase[2];
            
            $userTaskNumber = $currVarUser[0];
            $userTaskAnswer = $currVarUser[1];

            if ($baseTaskId > 0) {
                if ($baseTaskAnswer == $userTaskAnswer) {
                    $log_string .= $baseTaskNumber . ":correct|";
                } 
                else {
                    $log_string .= $baseTaskNumber . ":wrong-".$userTaskAnswer."|";
                }
            }
        }

        $log_string = substr($log_string, 0, -1);
        $log = R::dispense("logs");

        $log->user_id = $account->id;
        $log->user_name = $account->name;
        $log->typeof = "variant";
        $log->var_id = $variant->id;
        $log->task_num = "no";
        $log->task_id = "no";
        $log->task_userans = $userAnswers;
        $log->date = $currentDateTime;
        $log->data = $log_string;
        R::store($log);
    }
}

?>