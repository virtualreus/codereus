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

$numbers = [
    1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 
    11, 12, 13, 14, 15, 16, 17, 18, "19-21",
    22, 23, 24, 25, 26, 27
];

if (isset($_POST)) {
    $filename_cond = $_FILES["photo"]["name"];
    $tempname_cond = $_FILES["photo"]["tmp_name"];
    $task_files = $_FILES["add_task_files"];

    $number = $_POST["number"];
    $answers = trim($_POST["answers"]);
    $diff = $_POST["diff"];

    if (!in_array($number, $numbers)) {
        exit("error");
    }
    if ($number == "19-21") {
        $theory_games_answers = explode("|", $answers);

        if (count($theory_games_answers) == 3) {
            $current_task = 18;
            $current_answer = 0;
            for ($i = 0; $i < count($theory_games_answers); $i++) {
                $current_task += 1;
                $current_answer = $theory_games_answers[$i];

                $newname = (string)"task".$current_task;

                $new_task = R::dispense($newname);
                $new_task->condition =  "";
                $new_task->files = "";
                $new_task->answers = $current_answer;
                $new_task->difficulty = $diff;
                $currentDateTime = date('d-m-Y H:i');
                $new_task->date = $currentDateTime;
                R::store($new_task);

                $dirToCreateConds = "../../tasks/conds/".$current_task."/";
                $filename_to_base = "gametheory".$new_task->id.".jpg";
                $new_folder = $dirToCreateConds.$filename_to_base;
                
                if (!file_exists($dirToCreateConds)) {
                    mkdir($dirToCreateConds, 777, true);
                }
                $new_task->condition = $filename_to_base;
                R::store($new_task);
                move_uploaded_file($tempname_cond, $new_folder);


            }
            copy("../../tasks/conds/19/".$filename_to_base, "../../tasks/conds/20/".$filename_to_base);
            copy("../../tasks/conds/20/".$filename_to_base, "../../tasks/conds/21/".$filename_to_base);

            exit("success");

        }

        
    } else {
        $newname = (string)"task".$number;
        $taskFilesToBase = array();
    
        
        $new_task = R::dispense($newname);
        $new_task->condition =  "";
        $new_task->files = "";
        $new_task->answers = $answers;
        $new_task->difficulty = $diff;
        $currentDateTime = date('d-m-Y H:i');
        $new_task->date = $currentDateTime;
        R::store($new_task);
    
        $dirToCreateConds = "../../tasks/conds/".$number."/";
        if (!file_exists($dirToCreateConds)) {
            mkdir($dirToCreateConds, 777, true);
        }
    
        $filename_to_base = $number."_".$new_task->id.".jpg";
        $folder = "../../tasks/conds/".$number."/".$filename_to_base;
    
    
        $dirToCreateFiles = "../../tasks/files/".$number."/";
        if (!file_exists($dirToCreateFiles)) {
            mkdir($dirToCreateFiles, 777, true);
        }
        
        if (!empty($task_files["name"])) {
            for ($i = 0; $i < count($task_files["name"]); $i++) {
                $filenameFile = $task_files["name"][$i];
                $tmpnameFile = $task_files["tmp_name"][$i];
    
                $file_extension = pathinfo($filenameFile, PATHINFO_EXTENSION);
                $newFileName = $number."_".$new_task->id."_file_".chr(96 + $i + 1).".".$file_extension;
                array_push($taskFilesToBase, $newFileName);
    
                $folderFile = $dirToCreateFiles.$newFileName;
                move_uploaded_file($tmpnameFile, $folderFile);
    
            }
        } 
    
        $dictionary = [
            1 => 'a',
            2 => 'b',
            3 => 'c',
        ];
    
        $taskFilesToBase_string = implode("|", $taskFilesToBase);
    
        if (move_uploaded_file($tempname_cond, $folder)) {
            $new_task->condition = $filename_to_base;
            if ($taskFilesToBase_string) {
                $new_task->files = $taskFilesToBase_string;
            }
    
            R::store($new_task);
        }
        exit("success");
    }

}



?>