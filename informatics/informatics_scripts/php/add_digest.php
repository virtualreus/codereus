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

if (isset($_POST) and $account and $account->admin == 1) {
    $to_user = $_POST['to_user'];
    $data_tasks = $_POST['create_data'];

    $res_string = "";
    $usr_stirng = "";

    $data_tasks_array = explode("|", $data_tasks);

    foreach ($data_tasks_array as $task_elem) {
        $task_elem_array = explode(":", $task_elem);

        $task_number = $task_elem_array[1];
        $task_count = $task_elem_array[2];

        $tablename = "task" . $task_number;
        $tasks_rand = R::find($tablename, ' ORDER BY RAND() LIMIT ' . $task_count);
        if ($tasks_rand) {
            foreach ($tasks_rand as $tasktobase) {
                $res_string .= $task_number.":".$tasktobase->id.":".$tasktobase->answers."|";
                $usr_stirng .= $task_number.":".$tasktobase->id.":|";
            }
        }

    }

    $digest = R::dispense("variants");
    $digest->for_user = $to_user;
    $digest->type = '2';

    $currentDateTime = date('d-m-Y H:i');
    $digest->date = $currentDateTime;

    $digest->create_data = $res_string;
    $digest->user_data = $usr_stirng;
    R::store($digest);
    exit();


}


?>