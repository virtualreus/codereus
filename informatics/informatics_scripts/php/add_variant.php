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

function getRandomTaskIdFromDatabase($tableName) {
    $task = R::findOne($tableName, 'ORDER BY RAND() LIMIT 1');

    if ($task) {
        return $task->id.":".$task->answers;
    } else {
        return null;
    }
}



if (isset($_POST)) {
    $to_user = $_POST['to_user'];
    $data_tasks = $_POST['create_data'];


    $taskIds = []; // Массив для хранения "номер задания:id задания"

    $parts = explode('|', $data_tasks);
    var_dump($parts);

    foreach ($parts as $part) {
        list($taskName, $needTask) = explode(':', $part);
    
        $taskNumber = preg_replace("/[^0-9]/", '', $taskName);
        
        if ($needTask == 1) {
            $randomTaskId = getRandomTaskIdFromDatabase('task' . $taskNumber);
            
            if ($randomTaskId !== null) {
                $taskIds[] = $taskNumber . ':' . $randomTaskId;
            }
        } else {
            // Если задание не требуется, то добавляем "номер задания:0"
            $taskIds[] = $taskNumber . ':0:0';
        }
    }
    
    // Преобразуем массив в строку, разделяя значения символом "|"
    $taskIdsString = implode('|', $taskIds);
    
    $variant_to_base = R::dispense("variants");
    $variant_to_base->for_user = $to_user;
    $variant_to_base->type = '0';
    $currentDateTime = date('d-m-Y H:i');
    $variant_to_base->date = $currentDateTime;
    $variant_to_base->create_data = $taskIdsString;
    $variant_to_base->user_data = "";
    R::store($variant_to_base);
    exit();
}

?>