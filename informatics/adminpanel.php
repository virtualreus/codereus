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

if (!$account || $account->admin == 0) {
    exit('<script>
    setTimeout(() => { window.location.replace("../"); }, 200);
    </script>');
}

$all_users = R::findAll('users', 'admin = ?', [0]);

$allowed = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, "19-21", 22, 23, 24, 25, 26, 27);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="informatics_style/per_main.css">
    <link rel="stylesheet" href="informatics_style/per_variant.css">
    <link rel="stylesheet" href="informatics_style/adminpanel.css">
    
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="informatics_scripts/js/add_student.js"></script>
    <script src="informatics_scripts/js/add_task.js"></script>
    <script src="informatics_scripts/js/alerts.js"></script>
    <script src="informatics_scripts/js/add_variant.js"></script>
    <script src="informatics_scripts/js/add_digest.js"></script>
    <title>Админ панель.</title>
</head>
<body>
    <div id="post_result"></div>
    <main id="adminpanel">
        <div class="aheader">
            <h1>Добро пожаловать! &lt;Рандом фраза &gt;</h1>
        </div>

        <div class="astudents">
            <div class="astudents_header">
                <span class="astudents_name">Текущая таблица учеников:</span>

                <div class="astudents_header_buttons">
                    <button id="add_task">Добавить задание</button>
                    <img id="astudents_addnew" src="informatics_images/add_new.svg" alt="Добавить нового">
                </div>
            </div>
            <div class="astudents_table">

                <?php foreach ($all_users as $user) :?>

                <div class="astudent">
                    <div class="astudent_name">
                        <img class="astudent_img" src="informatics_images/login_face.svg" alt="">
                        <span class="astudent_username"><?=$user->name?></span>
                        <span class="astudent_stats">Время: <?=$user->date?></span>
                    </div>
                    <div class="astudents_buttons">
                        <a href="stats?id=<?=$user->id?>" class="abuttons_stats">Статистика</a>
                        <button class="abuttons_time" onclick="addDigest(<?=$user->id?>, '<?=$user->name?>')">Добавить сборник заданий</button>
                        <button class="abuttons_newvar" onclick="addVariant(<?=$user->id?>, '<?=$user->name?>')">Добавить вариант</button>

                        <button class="abuttons_delete">Удалить</button>
                    </div>
                </div>
                <?php endforeach; ?>
                


            </div>

        </div>
    </main>


    <div id="add_student">
        <div class="add_student_content">
            <span class="add_student_header">Добавление нового ученика.</span>

            <div class="add_student_prop">
                <span class="add_student_name">Введите имя:</span>
                <input type="text" id="add_student_name" placeholder="Имя и фамилия">
            </div>

            <div class="add_student_prop">
                <span class="add_student_name">Введите дату:</span>
                <input type="text" id="add_student_datetime" placeholder="Дата">
            </div>

            <div class="add_student_prop">
                <span class="add_student_name">Придумайте логин:</span>
                <input type="text" id="add_student_login" placeholder="Логин">
            </div>

            <div class="add_student_prop">
                <span class="add_student_name">Придумайте пароль:</span>
                <input type="text" id="add_student_password" placeholder="Пароль">
            </div>

            <button id="add_student_send">Добавить</button>
            
        </div>
        <img class="add_close" src="informatics_images/close.png"></img>
    </div>


    <div id="add_variant_user">
        <div class="add_variant_header">
            Добавить вариант:
        </div>
        <span class="add_variant_query">Выберите задания:</span>
        <div class="add_variant_tasks">
        
        <?php
            foreach ($allowed as $i) {
                echo '<div class="add_variant_checkbox_block">';
                echo '<input type="checkbox" class="add_variant_class" id="add_variant_task_' . $i . '">';
                echo '<label class="add_variant_class_text" for="add_variant_task_' . $i . '">№ ' . $i . '</label>';
                echo '</div>';

            }
        ?>
        </div>

        <button id="add_variant_send">Создать</button>
    </div>


    <div id="add_variant_digest">
        <div class="add_digest_header">Добавление нового сборника </div>
        <div class="add_digest_tasks">

            <?php
            foreach ($allowed as $i) :?>
                <div class="add_digest_task">
                    <span class="add_digest_num"><?=$i?> заданий: </span>
                    <input type="text" class="add_digest_input" id="add_digest_task_<?=$i?>" placeholder="Кол-во" maxlength="2" value="0">
                </div>
            <?php endforeach;?>
        </div>

        <button id="add_digest_send">Создать</button>
    </div>



    <div id="add_task_block">
        <div class="add_task_content">
            <span class="add_task_header">Добавление нового задания в базу.</span>

            <div class="add_task_elem">
                <span class="add_task_elem_desc">Введите номер задания. (целое число от 1 до 27, если теория игр, то вписать 19-21).</span>
                <input class="add_task_elem_input" id="add_task_number" type="text" placeholder="Номер задания:">
            </div>

            <div class="add_task_elem">
                <span class="add_task_elem_desc">Внесите сюда фото с условием задачи (если 19-21, фотография одна с условием для всех задач)</span>
                <input class="add_task_elem_input" id="add_task_cond" type="file" placeholder="Условие задачи">
            </div>

            <div class="add_task_elem">
                <span class="add_task_elem_desc">Внесите нужные файлы. Если два (26 - 27) - так и внести.</span>
                <input class="add_task_elem_input" id="add_task_files" type="file" name="add_task_files[]" multiple accept=".txt, .xlsx, .xls, .doc, .docx" placeholder="Файлы задачи">
            </div>

            <div class="add_task_elem">
                <span class="add_task_elem_desc">Введите верный ответ (если 19-21, то ответы вносить через |, пример: 11|13 24|14 )</span>
                <input class="add_task_elem_input" id="add_task_answers" type="text" placeholder="Ответ:">
            </div>

            <div class="add_task_elem">
                <span class="add_task_elem_desc">Введите сложность: (1 - легкий, 2 - уровень ЕГЭ, 3 - сложный (Джобс и т.п.), 4 - очень сложный (гробники и прочие) )<br>
                Если ничего не ввести - автоматически ставится уровень ЕГЭ</span>
                <input class="add_task_elem_input" id="add_task_difficulty" type="text" placeholder="Сложность:">
            </div>

            <button id="add_task_send">Добавить</button>
            
        </div>
        <img class="add_task_close" src="informatics_images/close.png"></img>
    </div>




    <div id="overlay" class="overlay"></div>

</body>
</html>