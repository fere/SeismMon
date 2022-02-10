<?php
echo memory_get_usage() . "\n"; //
header('Content-type: text/json; charset=utf-8'); //Указываем кодировку страницы
include 'phpQuery/phpQuery.php';    //Подключаем phpQuery
//setlocale(LC_ALL, 'rus');
date_default_timezone_set('Asia/Irkutsk');  //Устанавливаем таймзону в которой живем

$page = file_get_contents('http://seis-bykl.ru/index.php?ma=1');    //Указываем сайт который будем парсить
$page = iconv('windows-1251', 'utf-8', $page);  //Перекодируем сайт из windows-1251 в utf-8

$doc = phpQuery::newDocument($page);    //Создаем новый документ для парсинга
$entry = $doc->find('table:eq(8) tr:eq(1)');    //Парсим таблицу 7 строчку 2 (отсчет от нуля) 

$data['all'] = pq($entry)->html();  //Конвертируем вывод в html
$entry = $doc->find('table:eq(8) tr:eq(1) td:eq(0)');   //Парсим таблицу 7 строчку 2 ячейку 1 

$data['first'] = pq($entry)->text();    //Конвертируем вывод в текст
$entry = $doc->find('table:eq(8) tr:eq(1) td:eq(3)');   //Парсим таблицу 7 строчку 2 ячейку 4

$data['second'] = pq($entry)->text();   //Конвертируем вывод в текст
$entry = $doc->find('table:eq(8) tr:eq(1) td:eq(4)');   //Парсим таблицу 7 строчку 2 ячейку 5

$data['third'] = pq($entry)->text();    //Конвертируем вывод в текст
$alldata = $data['first'].' '.$data['second'].''.$data['third'];
//echo $alldata; //Показываем что напарсили

//echo ('<br>');

//Переводим время с гринвича на наше
$d = new DateTime($data['first']);  //Берем спарсенное время
$d->modify('+8 hour');  //Прибавляем 8 часов
$count_time = $d->format('Y-m-d H:i:s');
//echo $count_time; //Показываем что получилось

//echo ('<br>');

//Вычисляем сколько времени прошло с последнего землетрясения в секундах
$date = new DateTime( 'NOW' );  //Получаем текущую дату и время
$date2 = new DateTime($data['first']);  //Берем дату и время с напарсенного
$diffSeconds = $date->getTimestamp() - $date2->getTimestamp();  //Отнимаем текущую дату и время от тех что напарсили
//echo $diffSeconds;  //Показываем что получилось

//echo ('<br>');

//echo ($data['second']); //Выводим ЭК последнего землетрясения
$EK = $data['second'];  //Преобразуем вывод для условия

$PANIK = 0; //Переменная для паники

//Узнаем надо ли паниковать
if ($EK >= 11.00) {
    $PANIK = 1;
} else {
    $PANIK = 0;
}

//echo ('<br>');

//echo $date->format('Y-m-d H:i:s') . "\n"; //Выводим текущую дату и время

// echo ('<br>');
/*
//Проверяем какие ТЗ прописаны у нас и на сервере
if (date_default_timezone_get()) {
    echo 'date_default_timezone_set: ' . date_default_timezone_get() . '<br />';
}
if (ini_get('date.timezone')) {
    echo 'date.timezone: ' . ini_get('date.timezone');
}
*/
//
$EAQ=0;
if ($diffSeconds <= 60 and $PANIK == 1) {
    $EAQ =1;
} else {
    $EAQ =0;
}

//Проеобразуем нужные нам данные в JSON
$arr = array('LastInSec' => $diffSeconds, 'EK' => $EK, 'PANIK' => $PANIK, 'EAQ' => $EAQ);
echo json_encode($arr);

$doc = phpQuery::unloadDocuments($page); //Выгружаем парсер из памяти
?>
