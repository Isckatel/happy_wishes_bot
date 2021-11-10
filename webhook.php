<?php
//*************************************************************************************
//Первичный код взят с сайта https://evilcoder.ru/telegram-bot-bez-storonnih-bibliotek/
//*************************************************************************************
$body = file_get_contents('php://input'); //Получаем в $body json строку
$arr = json_decode($body, true); //Разбираем json запрос на массив в переменную $arr happy_wishes_bot
  
function cir_strrev($stroka){ //Так как функция strrev не умеет нормально переворачивать кириллицу, нужен костыль через массив. Создадим функцию
    preg_match_all('/./us', $stroka, $array); 
    return implode('',array_reverse($array[0]));
}
 
include_once ('tg.class.php'); //Меж дела подключаем наш tg.class.php
include_once ('db.class.php'); //Работа с БД
include_once ('token.php');  
include_once 'connection.php'; 

//Сразу и создадим этот класс, который будет написан чуть позже
//Сюда пишем токен, который нам выдал бот
$tg = new tg($token);
  
$sms = $arr['message']['text']; //Получаем текст сообщения, которое нам пришло.
//О структуре этого массива который прилетел нам от телеграмма можно узнать из официальной документации.
  
//Сразу и id получим, которому нужно отправлять всё это назад
$tg_id = $arr['message']['chat']['id'];
  
//Перевернём строку задом-наперёд используя функцию cir_strrev
$sms_rev = cir_strrev($sms);

$save = false;
//Рабочий код
if($save){
 
    // подключаемся к серверу
    // $link = mysqli_connect($host, $user, $password, $database) 
    //     or die("Ошибка " . mysqli_error($link)); 
    $link = mysqli_connect($host, $user, $password, $database);

    if (!$link) {  
        error_log("База данных недоступна!" . mysqli_error($link), 0);  
    } else {
        mysqli_set_charset($link, "utf8");    
        // экранирования символов для mysql
        $text = htmlentities(mysqli_real_escape_string($link, $sms)); 
        // $text = mb_convert_encoding($text, 'UTF-8', "auto"); 
        // создание строки запроса
        error_log("Сообщение: " . $text,0);
        $query ="INSERT INTO wishes VALUES(NULL, '$text', CURRENT_DATE(), true, '$tg_id', 0, 0)";
        // выполняем запрос
        $result = mysqli_query($link, $query);

        if($result)
        {
            // echo "<span style='color:blue;'>Данные добавлены</span>";
            $sms_rev .= "Пожелание добавлено!";
        } else {
            error_log("Ошибка " . mysqli_error($link), 0);
        }

    }

    // закрываем подключение
    mysqli_close($link);
}

$save2 = false;
//Не работает
if($save2) {
    $db = new db();
    $sms_rev .= $db->addMess($sms, $tg_id);    
}

//Используем наш ещё не написанный класс, для отправки сообщения в ответ
$tg->send($tg_id, $sms_rev);

exit('ok'); //Обязательно возвращаем "ok", чтобы телеграмм не подумал, что запрос не дошёл
?>