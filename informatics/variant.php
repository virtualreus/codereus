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

$variant_id = $_GET['id'];

$variant = R::load('variants', $variant_id);

$date = $variant->date;
$timestamp = strtotime($date);

$date_header = date("d.m.Y", $timestamp);

if (!$account || !$variant->id || (($account->admin == 0 )&& ($variant->for_user != $account->id))) {
    exit('<script>
    setTimeout(() => { window.location.replace("../"); }, 200);
    </script>');
}


$variant_type = $variant->type;
$type_description = '';
if ($variant_type == 0) {
    $type_description = 'Неограниченное количество попыток';
} elseif ($variant_type == 1) {
    $type_description = 'Только одна попытка.';
} elseif ($variant_type == 2) {
    $type_description = 'Сборник заданий.';
} else {
    $type_description = 'Неизвестный тип';
}

$variant_data = explode("|", substr($variant->create_data, 0 ,-1));
$usr_data = explode("|", substr($variant->user_data, 0, -1 ));

$solved_count = 0;
if ($variant->type == "2") {
    foreach ($usr_data as $user_given) {
        if(explode(":", $user_given)[2]) {
            $solved_count += 1;
        }
    }
}

$color_solved = "red";
if ($solved_count == count($variant_data)) {
    $color_solved = "green";
}
elseif ($solved_count < count($variant_data) and $solved_count > 0) {
    $color_solved = "#c27e00;";
}
elseif ($solved_count == 0) {
    $color_solved = "#c00000;";
}

$ball = array(
    0 => 0,
    1 => 7,
    2 => 14,
    3 => 20,
    4 => 27,
    5 => 34,
    6 => 40,
    7 => 43,
    8 => 46,
    9 => 48,
    10 => 51,
    11 => 54,
    12 => 56,
    13 => 59,
    14 => 62,
    15 => 64,
    16 => 67,
    17 => 70,
    18 => 72,
    19 => 75,
    20 => 78,
    21 => 80,
    22 => 83,
    23 => 85,
    24 => 88,
    25 => 90,
    26 => 93,
    27 => 95,
    28 => 98,
    29 => 100
);


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="informatics_style/per_main.css">
    <link rel="stylesheet" href="informatics_style/per_variant.css">
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
    <script src="informatics_scripts/js/alerts.js"></script>
    <script src="informatics_scripts/js/send_variant.js"></script>
    <script src="informatics_scripts/js/check_digest.js"></script>
    <title>Вариант от <?=$date_header?></title>
</head>
<body>

<div class="float_window">
    <a href="/" class="float_window_link"><i class="fa fa-arrow-left"></i>Выйти на главную.</a>
    <a href="task" class="float_window_link"><i class="fa fa-arrow-left"></i>Выйти к заданиям.</a>
</div>

    <?php if ($variant->type == 2) :


        ?>
        <main id="var_main">
            <div class="var_header">
                <h1 class="var_name">Сборник от <?=$date_header?></h1>
                <div class="var_header_desc">
                    <span class="var_header_desc_type"><?=$type_description?></span>

                     <span class="var_header_desc_solved" style="color: <?=$color_solved?>">Решено: <?=$solved_count?>/<?=count($variant_data)?></span>
                </div>
            </div>

            <?php

            for ($var_task_index = 0; $var_task_index < count($variant_data); $var_task_index++) :
                $var_task_data = explode(":", $variant_data[$var_task_index]);

                $var_task_num = $var_task_data[0];
                $var_task_id = $var_task_data[1];

                $var_tablename = 'task'.$var_task_num;

                $var_echo = R::load($var_tablename, $var_task_id);

                $usr_task_data = explode(":", $usr_data[$var_task_index]);
                $block_style = "";
                if ($usr_task_data[2]) {
                    $block_style = "border: 1px solid #b9d4b8; box-shadow: rgba(6, 125, 0, 0.3) 4px 4px 80px 0px;";
                }

                if ($var_echo->id) :
                    $var_echo_cond = $var_echo->condition;

                    ?>
                    <div class="var_task" style="<?=$block_style?>">
                        <div class="var_task_header" style="<?=$usr_task_data[2] ? 'color: green' : ''?>"><span>&bull;&nbsp;&nbsp;Задание №<?=$var_task_num?> (id: <?=$var_task_id?>)</span></div>
                        <div class="var_task_cond"><img src="tasks/conds/<?=$var_task_num?>/<?=$var_echo_cond?>" alt=""></div>
                        <div class="var_task_files">


                            <?php if ($var_echo->files) : ?>
                                <?php $files_array = explode("|", $var_echo->files) ?>
                                <div class="main_task_block_files"><span>Файлы:</span>
                                    <?php
                                    foreach ($files_array as $file) :
                                        $needle = "file_";
                                        $pos = strrpos($file, $needle);
                                        if ($pos !== false) {
                                            $newFilename = substr($file, $pos + strlen($needle));
                                        }?>

                                        <a href="tasks/files/<?=$var_task_num?>/<?=$file?>" download="<?=$file?>">Файл <?=$newFilename?></a>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>


                        </div>
                        <div class="var_task_ans">
                            <?php if ($usr_task_data[2]): ?>
                                <input type="text" class="var_task_ans_input" readonly style="color: #056305; background-color: rgb(212 212 212); border: 1px solid #b9d4b8; width: 100%" placeholder="Ответ:" value="<?=$usr_task_data[2]?>">
                            <?php else : ?>
                                <input type="text" class="var_task_ans_input" style="width: 100%" placeholder="Ответ:">
                                <button class="var_task_ans_send" onclick="checkAns(this, <?=$variant_id?>, <?=$var_task_index?>)">Проверить</button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif;?>
            <?php endfor;?>

        </main>
    <?php else : ?>
        <?php if (!$variant->user_data) : ?>

    <main id="var_main">
        <div class="var_header">
            <h1 class="var_name">Вариант от <?=$date_header?></h1>
            <div class="var_header_desc">
            <span class="var_header_desc_type"><?=$type_description?></span>

            <!-- <span class="var_header_desc_solved p_non_full">Решено: 0/27</span> -->
            </div>
        </div>
    
        <?php
        
        for ($var_task_index = 0; $var_task_index < count($variant_data); $var_task_index++) :
            $var_task_data = explode(":", $variant_data[$var_task_index]);

            $var_task_num = $var_task_data[0];
            $var_task_id = $var_task_data[1];

            $var_tablename = 'task'.$var_task_num;
           
            $var_echo = R::load($var_tablename, $var_task_id);


            if ($var_echo->id) :
                $var_echo_cond = $var_echo->condition;

        ?>
            <div class="var_task">
                <div class="var_task_header"><span>&bull;&nbsp;&nbsp;Задание №<?=$var_task_num?> (id: <?=$var_task_id?>)</span></div>
                <div class="var_task_cond"><img src="tasks/conds/<?=$var_task_num?>/<?=$var_echo_cond?>" alt=""></div>
                <div class="var_task_files">


                <?php if ($var_echo->files) : ?>
                <?php $files_array = explode("|", $var_echo->files) ?>
                <div class="main_task_block_files"><span>Файлы:</span> 
                <?php 
                foreach ($files_array as $file) : 
                    $needle = "file_";
                    $pos = strrpos($file, $needle);
                    if ($pos !== false) {
                        $newFilename = substr($file, $pos + strlen($needle));
                    }?>

                    <a href="tasks/files/<?=$var_task_num?>/<?=$file?>" download="<?=$file?>">Файл <?=$newFilename?></a>
                <?php endforeach; ?>
                </div>
            <?php endif; ?>


                </div>
                <div class="var_task_ans">
                    <input type="text" class="var_task_ans_input" id="input_task_<?=$var_task_num?>" style="width: 100%" placeholder="Ответ:">
                </div>
            </div> 
            <?php endif;?>
        <?php endfor;?>

        <button id="send_variant_answers">Отправить</button>
    </main>

    <?php else : 
        $userAnswers = $variant->user_data;

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
        
        

        ?>
        <main id="var_main">
            <div class="var_header">
                <h1 class="var_name">Вариант от <?=$date_header?></h1>
                <div class="var_header_desc">
                <span class="var_header_desc_type"><?=$type_description?></span>
                </div>
            </div>

            <div class="var_results">
                <h2 class="var_result_header">Решение варианта завершено.</h2>
                <span class="var_result_unheader">Ваш результат:</span>


                    <div class="var_result_table">
                        <div class="var_result_table_header">
                            <div class="var_result_table_numb">№</div>
                            <div class="var_result_table_ans">Ответ:</div>
                            <div class="var_result_table_res">Балл:</div>
                        </div>

                        <?php 
                        $max_mark = 0;
                        $user_mark_sum = 0;

                        for ($i = 0; $i < count($base_array); $i++) :
                                $currVarBase = $base_array[$i];
                                $currVarUser = $user_array[$i];

                                $baseTaskNumber = $currVarBase[0];
                                $baseTaskId = $currVarBase[1];
                                $baseTaskAnswer = $currVarBase[2];
                                
                                $userTaskNumber = $currVarUser[0];
                                $userTaskAnswer = $currVarUser[1];
                                $marks = 0;
                                if ($baseTaskId > 0) : 
                                    if ($baseTaskNumber >= 1 && $baseTaskNumber <= 25) {
                                        $marks = ($baseTaskAnswer == $userTaskAnswer) ? 1 : 0;

                                        $max_mark += 1;

                                    } elseif ($baseTaskNumber >= 26 && $baseTaskNumber <= 27) {
                                        $baseTaskAnswerArray = explode(" ", $baseTaskAnswer);
                                        $userTaskAnswerArray = explode(" ", $userTaskAnswer);
                                        $marks = ($baseTaskAnswerArray[0] == $userTaskAnswerArray[0] ? 1 : 0) + ($baseTaskAnswerArray[1] == $userTaskAnswerArray[1] ? 1 : 0);
                                        $max_mark += 2;
                                }

                                $user_mark_sum += $marks;

                                $color = ""; // Инициализация переменной $color
                                if ($baseTaskNumber >= 1 && $baseTaskNumber <= 25) {
                                    $color = ($marks == 1) ? "vrt_correct" : "vrt_incorrect";
                                } elseif ($baseTaskNumber >= 26 && $baseTaskNumber <= 27) {
                                    if ($marks == 1) {
                                        $color = "vrt_semicorrect";
                                    } elseif ($marks == 2) {
                                        $color = "vrt_correct";
                                    } else {
                                        $color = "vrt_incorrect";
                                    }
                                } else {
                                    $color = "vrt_incorrect";
                                }
                                ?>

                                
                                <div class="var_result_table_row">
                                    <div class="var_result_table_numb"><?=$baseTaskNumber?></div>
                                    <div class="var_result_table_ans"><?=$userTaskAnswer?></div>
                                    <div class="var_result_table_res <?=$color?>"><?=$marks?></div>
                                </div>
                                <?php endif; ?>
                        <?php endfor;?>
                    </div>

                    <div class="result_marks">Вы набрали <b><?=$user_mark_sum?></b> баллов из <b><?=$max_mark?></b> возможных.</div>
                    <div class="result_vt">При переводе во вторичную систему оценивания это <b><?=$ball[$user_mark_sum]?> баллов</b></div>

                    <div class="result_buttons">
                        <button class="res_butt"><a href="/">На главную</a></button>
                        <button id="var_reset" class="res_butt">Попробовать еще раз</button>
                    </div>
            </div>
        </main>
    <?php endif;?>
    <?php endif;?>
</body>
</html>
