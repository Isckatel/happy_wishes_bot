<?php
class db {    
    private $res;

    public function addMess ($sms, $tg_id){
        
        include_once 'connection.php';
        // подключаемся к серверу
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
                $this->$res = "Пожелание добавлено!";
            } else {
                error_log("Ошибка " . mysqli_error($link), 0);
            }

        }

        // закрываем подключение
        mysqli_close($link);
        return $this->$res;
    }
    
    public function getMess(){

    }
}
?>