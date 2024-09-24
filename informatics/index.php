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


$months = [
    1 => 'января',
    2 => 'февраля',
    3 => 'марта',
    4 => 'апреля',
    5 => 'мая',
    6 => 'июня',
    7 => 'июля',
    8 => 'августа',
    9 => 'сентября',
    10 => 'октября',
    11 => 'ноября',
    12 => 'декабря',
];


$account = R::findOne('users', 'id = ?', array($_SESSION['id']));
$account_firstname = explode(' ', $account->name)[0];

if (!$account) {
    exit('<script>
    setTimeout(() => { window.location.replace("../login"); }, 10);
    </script>');
}

$variants = R::findAll('variants', 'for_user = ?', [$account->id]);

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="informatics_style/per_main.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">



	<link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="/favicon/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/favicon/favicon-16x16.png">
	<link rel="manifest" href="/favicon/site.webmanifest">
	<link rel="mask-icon" href="/favicon/safari-pinned-tab.svg" color="#5bbad5">
	<meta name="msapplication-TileColor" content="#da532c">
	<meta name="theme-color" content="#000000">

	
	
    <title>Личный кабинет</title>
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
                        <li class="personal_menu_active"><a href="/informatics"><i class="fa fa-mortar-board personal_menu_faicon" aria-hidden="true"> </i> </i>Главная</a></li>
                        <li><a href="task"><i class="fa fa-mortar-board personal_menu_faicon" aria-hidden="true"> </i>Задания</a></li>
                        <li><a href="/logout"><i class="fa fa-mortar-board personal_menu_faicon" aria-hidden="true"> </i>Выход</a></li>
                    </ul>
                </div>
            </div>



            <div id="personal_main">
                <div class="personal_main_header">
                    <span class="personal_main_header_text">Добро пожаловать, <?=$account_firstname?>. Ваши варианты: </span>
                    
                    <!-- div id="personal_add_new_var"><img class="personal_add_new" src="informatics_images/add_new.svg" alt=""></div -->
                </div>
                
                <div class="personal_variants">
               
                    
                    <?php foreach (array_reverse($variants) as $variant) : 
                        if ($variant->type == 0 || $variant->type == 1) :
                            $var_process = (!empty($variant->user_data)) ? 'var_solved' : 'var_non_solved';
                            $var_process_text = (!empty($variant->user_data)) ? 'Решено' : 'Не решено';
                            $var_process_color = (!empty($variant->user_data)) ? 'p_full' : 'p_non_full';

                            $varDateString = $variant->date;
                            $var_components = explode(" ", $varDateString);
                            $var_date_components = explode("-", $var_components[0]);
                            $var_time_components = explode(":", $var_components[1]);

                            $var_day = $var_date_components[0];
                            $var_month = $months[$var_date_components[1]];
                            $vardate_result_string = $var_day . " " . $var_month;
                            ?>
                            <a href="variant?id=<?=$variant->id?>" class="personal_variant <?=$var_process?>">
                                <div class="personal_variant_icon"><img src="informatics_images/var.svg" alt=""></div>
                                <div class="personal_variant_header"><span><b>Вариант от <?=$vardate_result_string?> </b></span></div>
                                <div class="personal_variant_desc">
                                    <div>Тип: <b>Домашнее задание</b></div>
                                    <div>Создан: <?=$vardate_result_string?> в <?=$var_components[1]?></div>
                                    <div class="<?=$var_process_color?>"><b><?=$var_process_text?></b></div>
                                    
                                </div>
                            </a>


                        <?php elseif ($variant->type == 2) :
                            $var_process = "";

                            $varDateString = $variant->date;
                            $var_components = explode(" ", $varDateString);
                            $var_date_components = explode("-", $var_components[0]);
                            $var_time_components = explode(":", $var_components[1]);

                            $var_day = $var_date_components[0];
                            $var_month = $months[$var_date_components[1]];
                            $vardate_result_string = $var_day . " " . $var_month;


                            $variant_data = explode("|", substr($variant->create_data, 0 ,-1));
                            $usr_data = explode("|", substr($variant->user_data, 0, -1 ));


                            $solved_count = 0;

                            foreach ($usr_data as $user_given) {
                                if(explode(":", $user_given)[2]) {
                                    $solved_count += 1;
                                }
                            }


                            $color_solved = "red";
                            if ($solved_count == count($variant_data)) {
                                $color_solved = "green";
                                $var_process = "var_solved";
                            }
                            elseif ($solved_count < count($variant_data) and $solved_count > 0) {
                                $color_solved = "#c27e00;";
                                $var_process = "var_solving";
                            }
                            elseif ($solved_count == 0) {
                                $color_solved = "#c00000;";
                                $var_process = "var_non_solved";
                            }


                            ?>
                            <a href="variant?id=<?=$variant->id?>" class="personal_variant <?=$var_process?>">
                                <div class="personal_variant_icon"><img src="informatics_images/sbornik.svg" alt=""></div>
                                <div class="personal_variant_header"><span><b>Сборник от <?=$vardate_result_string?> </b></span></div>
                                <div class="personal_variant_desc">
                                    <div>Тип: <b>Сборник заданий</b></div>
                                    <div>Создан: <?=$vardate_result_string?> в <?=$var_components[1]?></div>
                                    <div style="color: <?=$color_solved?>">Решено: <b><?=$solved_count?>/<?=count($variant_data)?></b></div>

                                </div>
                            </a>
                        <?php endif; ?>     
                    <?php endforeach; ?>



                    <div class="personal_variant"></div>
                    <div class="personal_variant"></div>
                </div>

            </div>
        </div>

    </main>

</body>
</html>