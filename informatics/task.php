<?php 
require "../libs/db.php";

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

if (!$account) {
    exit('<script>
    setTimeout(() => { window.location.replace("../"); }, 200);
    </script>');
}

$task_number = $_GET['number'];
$task_id = $_GET['id'];
/*$allowed = array(
    1, 2, 3, 4, 5, 6, 7, 8, 9, 10,
    11, 12, 13, 14, 15, 16, 17, 18, 19, 20,
    21, 22, 23, 24, 25, 26, 27
);*/

$allowed = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27);

if (in_array($task_number, $allowed)) {
    $taskTableName = 'task' . $task_number;
    $tasks = R::find($taskTableName);

    $randomIndex = array_rand($tasks);
    $randomTask = $tasks[$randomIndex];
}

$err_non_task = 0;

if ($task_id) {
    $randomTask = R::load('task' . $task_number, $task_id);
    if (!$randomTask->id) {
        $err_non_task = 1;
    }
} else {
    if (in_array($task_number, $allowed)) {
        $taskTableName = 'task' . $task_number;
        $tasks = R::find($taskTableName);
        $randomIndex = array_rand($tasks);
        $randomTask = $tasks[$randomIndex];
    }
}

$diff = $randomTask->difficulty;
if (!$diff) {
    $diff = 2;
}

switch ($diff) {
    case 1:
        $task_color = 'green';
        $task_text = 'Легкий уровень';
        break;
    case 2:
        $task_color = 'orange';
        $task_text = 'Уровень ЕГЭ';
        break;
    case 3:
        $task_color = 'red';
        $task_text = 'Усложненный уровень';
        break;
    case 4:
        $task_color = 'black';
        $task_text = 'Очень сложный уровень';
        break;
    default:
        $task_color = 'black';
        $task_text = 'Недопустимое значение уровня сложности';
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="informatics_style/per_main.css">
    <link rel="stylesheet" href="informatics_style/main_task.css">

    <link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="/favicon/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/favicon/favicon-16x16.png">
	<link rel="manifest" href="/favicon/site.webmanifest">
	<link rel="mask-icon" href="/favicon/safari-pinned-tab.svg" color="#5bbad5">
	<meta name="msapplication-TileColor" content="#da532c">
	<meta name="theme-color" content="#000000">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="informatics_scripts/js/check_task_main.js"></script>
    <script src="informatics_scripts/js/alerts.js"></script>
    <title>Задания</title>
</head>
<body>
    
<main id="personal">

<div id="personal_block">
    <div id="personal_data">
        <div id="personal_icon">
            <img class="personal_img" src="informatics_images/login_face.svg" alt="">
            <span class="personal_name"><?=$account->name?></span>
            <ul class="personal_date">Время занятий:
                 <li><?=$account->date?></li>
            </ul>
        </div>
        <div id="personal_menu">
            <div class="personal_menu_header">Меню</div>
            <ul class="personal_menu_list">
                    <li><a href="/informatics"><i class="fa fa-mortar-board personal_menu_faicon" aria-hidden="true"> </i> </i>Главная</a></li>
                    <li class="personal_menu_active"><a href="task"><i class="fa fa-mortar-board personal_menu_faicon" aria-hidden="true"> </i>Задания</a></li>
                    <li><a href="/logout"><i class="fa fa-mortar-board personal_menu_faicon" aria-hidden="true"> </i>Выход</a></li>
            </ul>
        </div>
    </div>



    <div id="main_task">
        <div class="main_task_header">
            <span class="main_task_header_text">Konichiwa, <?=$account_firstname?>! Выберите задание: </span>
            <input type="text" id="task_number" placeholder="Номер:" value="<?=$task_number?>">
            <input type="text" id="task_id" placeholder="id:" value="<?=$task_id?>">

            <button id="find_task">Найти</button>
            <button id="task_refresh">Обновить <i class="fa fa-refresh" aria-hidden="true"></i></button>
        </div>

        <div id="main_task_block">

            <?php if (in_array($task_number, $allowed) and $err_non_task == 0) :?>
                <div class="main_task_block_header"><span>•&nbsp;&nbsp;Задание №<?=$task_number?> (id: <?=$randomTask->id?>) | <span style="color: <?=$task_color?>"><?=$task_text?></span></span></div>
                <div class="main_task_block_cond"><img src="tasks/conds/<?=$task_number?>/<?=$randomTask->condition?>" alt=""></div>

            <?php

                if ($randomTask->files) : ?>
                <?php $files_array = explode("|", $randomTask->files) ?>
                <div class="main_task_block_files"><span>Файлы:</span> 
                <?php 
                foreach ($files_array as $file) : 
                    $needle = "file_";
                    $pos = strrpos($file, $needle);
                    if ($pos !== false) {
                        $newFilename = substr($file, $pos + strlen($needle));
                    }?>

                    <a href="tasks/files/<?=$task_number?>/<?=$file?>" download="<?=$file?>">Файл <?=$newFilename?></a>
                <?php endforeach; ?>
                </div>
            <?php endif; ?>


                <div class="main_task_block_ans">
                    <input type="text" id="main_task_block_ans_input" placeholder="Ответ:">
                    <button id="main_task_block_ans_send" data-number-id="<?=$randomTask->id?>">Проверить</button>
                </div>
            <?php elseif (!$task_number) : ?>
                <div class="task_error"><i class="fa fa-info-circle task_error_icon" aria-hidden="true"></i>Введите номер задания (от 1 до 27).</div>
            <?php elseif ($err_non_task == 1) : ?>
                <div class="task_error"><i class="fa fa-info-circle task_error_icon" aria-hidden="true"></i>Задания <?=$task_number?> с id <?=$task_id?> не существует :(</div>
            <?php else :?>
                <div class="task_error"><i class="fa fa-exclamation-triangle task_error_icon" aria-hidden="true"></i> Введен некорректный номер задания.
                 <br>На данный момент к решению доступны 1-27.</div>
            <?php endif;?>

        </div>

        <script>
                document.getElementById("find_task").addEventListener("click", function() {
                    var taskNumber = document.getElementById("task_number").value;
                    var taskId = document.getElementById("task_id").value;
                    let newlink = "?number=" + taskNumber;
                    console.log(taskId)
                    if (taskId) {
                        newlink += "&id=" + taskId
                    }
                    window.location.href = newlink;
                });

                document.getElementById('task_refresh').addEventListener('click', function() {
                    location.reload();
                });
        </script>


    </div>
</div>

</main>