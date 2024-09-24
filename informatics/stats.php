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

if (!$account || $account->admin == 0) {
    exit('<script>
    setTimeout(() => { window.location.replace("../"); }, 200);
    </script>');
}

$months = [
    "января", "февраля", "марта", "апреля", "мая", "июня",
    "июля", "августа", "сентября", "октября", "ноября", "декабря"
];


$userid = $_GET["id"];
$user = R::findOne('users', 'id = ?', [$userid]);

$user_logs = R::findAll("logs", "user_id = ?", [$user->id]);



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
    <title>Статистика <?=$user->name?></title>
</head>
<body>


<main id="adminpanel">
        <div class="aheader">
            <h1>Статистика пользователя: <?=$user->name?></h1>
        </div>

        <div class="astudents">
            <div class="astudents_header">

            <div class="stats_table">

                <div class="stats_table_header">
                    <div class="stats_table_header_date">Дата</div>
                    <div class="stats_table_header_type">Вариант/Задание</div>
                    <div class="stats_table_header_num">Номер</div>
                    <div class="stats_table_header_id">ID</div>
                    <div class="stats_table_header_event">Событие</div>
                </div>

                <?php foreach (array_reverse($user_logs) as $log) : 
                    $date_log = $log->date;

                    // Разбиваем строку на дату и время
                    list($date, $time) = explode(' ', $date_log);
                    
                    // Разбиваем дату на составляющие
                    list($day, $month, $year) = explode('-', $date);
            
                    
                    $monthName = $months[intval($month) - 1];
                    
                    $fixedDate = "$day $monthName в $time";
                    
                    
                    ?>
                    <div class="stats_table_row">
                    <?php if ($log->typeof == "task") :?>

                        <div class="stats_table_header_date"><?=$fixedDate?></div>
                        <div class="stats_table_header_type">Отправлено задание</div>
                        <div class="stats_table_header_num"><?=$log->task_num?></div>
                        <div class="stats_table_header_id"><?=$log->task_id?></div>
                        <?php if ($log->data == "answer_incorrect") :?>
                            <div class="stats_table_header_event" style="background: #ffb8b8;">Неверно. Дан ответ: <?=$log->task_userans?></div>
                        <?php elseif ($log->data == "answer_correct"): ?>
                            <div class="stats_table_header_event" style="background: #b7eca8;">Верно. Дан ответ: <?=$log->task_userans?></div>
                        <?php endif;?>

                    <?php elseif ($log->typeof == "variant"): 
                        $inputString = $log->data;

                        // Разбиваем строку на элементы, разделенные символом "|"
                        $elements = explode('|', $inputString);
                        
                        // Создаем массив для хранения результата
                        $resultArray = [];
                        
                        foreach ($elements as $element) {
                            // Разбиваем каждый элемент на ключ и значение
                            list($key, $value) = explode(':', $element);
                        
                            // Разделяем значение на статус и ответ
                            $statusAndAnswer = explode('-', $value);
                        
                            // Определяем статус
                            $status = $statusAndAnswer[0] === 'correct' ? '<b style="color: green;">Верно</b>' : '<b style="color: red;">Неверно</b>';
                        
                            // Формируем ответ
                            $answer = isset($statusAndAnswer[1]) ? " дан ответ: " . $statusAndAnswer[1] : "";
                        
                            // Добавляем элемент в результат
                            $resultArray[] = "$key - $status$answer";
                        }
                        
                        $outputString = implode(';<br> ', $resultArray);
                        
                        ?>
                        <div class="stats_table_header_date"><?=$fixedDate?></div>
                        <div class="stats_table_header_type">Отправлен вариант</div>
                        <div class="stats_table_header_num"></div>
                        <div class="stats_table_header_id"><?=$log->var_id?></div>
                        <div class="stats_table_header_event"><?=$outputString?></div>

                    <?php endif;?>
                    
                    </div>
                <?php endforeach;?>

                </div>
            </div>

        </div>
    </main>

</body>
</html>