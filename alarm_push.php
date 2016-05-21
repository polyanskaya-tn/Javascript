<?php

require('../base/main.php');

//удалить call-файлы абонента
function clearFiles($files) 
{
	$dir_outgoing = "/var/spool/asterisk/outgoing/";
	foreach ($files AS $filename) {
		if ($filename != "") {
			$system1 = system('rm '.$dir_outgoing.$filename);
			if ($system1 != "")
				echo $system1;
		}
	}
}

$abonent = (isset($_GET['abonent'])) ? replace_html($_GET['abonent']) : "";	// телефонный номер абонента
$hours = isset($_GET['hours']) ? intval($_GET['hours']) : 0;
$minutes = isset($_GET['minutes']) ? intval($_GET['minutes']) : 0;
$is_on = isset($_GET['is_on']) ? intval($_GET['is_on']) : 0;

//--------- вычисление даты для будильника ----------
$cur_date = time();
$alarm_date = mktime($hours, $minutes, 0); //пробуем установить будильник на сегодня
if ($alarm_date - $cur_date <= 0) //на завтра, добавить 1 день
	$alarm_date = mktime($hours, $minutes, 0, date("n"), (integer)date("j")+1);

$arr_date = getdate($alarm_date);
$year = $arr_date["year"];
$month = sprintf("%'.02d", $arr_date["mon"]);
$day = sprintf("%'.02d", $arr_date["mday"]);
$hours = sprintf("%'.02d", $hours);
$minutes = sprintf("%'.02d", $minutes);
//YYYYMMDDhhmm
$alarm = $year.$month.$day.$hours.$minutes;
//----------------------------------------------------

//----------- работа с таблицей БД -------------------
$abonent = mysql_real_escape_string($abonent);
$query = "SELECT * FROM `alarm_clock` WHERE SIP_='$abonent'";
$sql = new sql($query);
if (!$sql->is_ok()) {
	echo "запрос #1 : ".$sql->error_msg();
	exit();
}

$id = 0;
$files = array();
foreach($sql->get_row() AS $row) {
	$filename = (isset($row->FILE_)) ? $row->FILE_ : "";
	if ($filename != "")
		$files[] = $filename;
	if (isset($row->ID_)) 
		$id = $row->ID_; //id последней записи в массиве
}
//удалить файлы
if ( count($files) > 0 )
	clearFiles($files);

$filename = "";
if ($is_on == 1) {	//включить
	//создать call-файл
	//проверки на ошибки создания
	$func = setAlarmClock($abonent, $alarm, $filename);
	if ($func['is_error'] == 1) {
		echo "Ошибка : ".$func['msg'];
		exit();
	}
}

if ($id == 0) {	//в таблице БД нет записей
	
	//вставить запись в БД
	if ($is_on == 1) {
		$query = "INSERT INTO `alarm_clock` 
		(SIP_, DATE_, TIME_, DAYS_, SWITCH_IS_ON_, FILE_) 
		VALUES ('$abonent', '$year-$month-$day', '$hours:$minutes', '0000000', 1, '$filename')";
		
		$sql = new sql($query, "w");
		if (!$sql->is_ok()) {
			echo "запрос #2 : ".$sql->error_msg();
			exit();
		}
	}	
}
else {	//в таблице БД были записи
	
	//оставить одну запись в БД
	$query = "DELETE FROM `alarm_clock` WHERE SIP_='$abonent' AND ID_<>'$id'";
	$sql = new sql($query, "w");
	if (!$sql->is_ok()) {
		echo "запрос #3 : ".$sql->error_msg();
		exit();
	}
	//обновить ее
	$query = "";
	if ($is_on == 1) {
		$query = "UPDATE `alarm_clock` SET FILE_='$filename', DATE_='$year-$month-$day', 
		TIME_='$hours:$minutes', SWITCH_IS_ON_=1, DAYS_='0000000' WHERE ID_='$id'";
	}
	else {
		$query = "UPDATE `alarm_clock` SET SWITCH_IS_ON_=0 WHERE ID_='$id'";
	}
		
	if ($query != "") {
		$sql = new sql($query, "w");
		if (!$sql->is_ok()) {
			echo "запрос #4 : ".$sql->error_msg();
			exit();
		}
	}
}

?>
