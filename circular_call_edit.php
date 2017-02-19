<?php 
require('../base/main.php');

$GROUP = "admin"; // к какой группе относится данная страница
$ITEM = "Услуги / Циркулярное оповещение";
$SUBITEM = "Списки абонентов - редактирование";
$TITLE = "Списки абонентов - редактирование";

$HELP = "javascript: window.open('../help/cc_admin/cc_admin.html#call_list_c_edit', '', 'toolbar=no,menubar=yes,scrollbars=yes,resizable=yes,width=750,height=650,top=0'); history.go('0');";

$is_fronted = true;
$html = new HtmlObject($is_fronted);

global $param;

require ('circular_call_edit_param.php');
$object_edit = new Object_Edit($param, $is_fronted);

if(isset($_POST['do_change'])) {
	$html->html_stop();
}

?>


<script src="../base/search.js"></script>

<script>

//запрос из БД списка имен направлений по Ajax
function get_provider_names() {
	JsHttpRequest.query(
         'circular_call_edit_answer.php', 
         {
         },
         function(result, errors) {
				if (result.is_error == 1)
					errMessage = result.error_msg;
				if (result.providers != null)
					trunks = result.providers+',';
         },
         false
	);
}

//распарсить данные строка --> двумерный массив
function clStrToArray(str)
{
	var arr = [];
	var pattern = /((([A-Za-z]+):([\w@\.]+))[,\n]*)+/mg;

	//проверка данных на соответствие формату
	if ( pattern.test(str) ) {
		//разбиваем сначала построчно, потом по разделительным запятым
		var buf = str.split('\n');
		for (var row in buf) {
			arr[row] = buf[row].split(',');
			//проверка чтобы столбцов было максимум 4 
			if (arr[row].length > 4)
				//обрезать	
				var col = arr[row].length;
				while (col > 4) {
					col--;
					arr[row].pop();
				}
		}
	}
	else 
		errMessage = 'Ошибка. Неправильный формат данных';
	return arr;
}

//двумерный массив в строку
function clArrayToStr(arr)
{
	var str = '';
	for (var row in arr) {
		if (row > 0) str += '\n';
		for (var col in arr[row]) {
			if (col > 0) str += ','; 
			str += arr[row][col];
		}
	}
	return str;
}

function checkData(phone, phone_type) {
	if (phone_type == 'MAIL') {
		var pattern=/^[\w-]+(\.[\w-]+)*@([\w-]+\.)+[a-zA-Z]{2,7}$/;
		if (!pattern.test(phone)) {
			setError('Ошибка. Неверный формат email:'+phone+'!'); 
			return false;	
		}
		return true;	
	}
	else if (phone_type == 'SMS') {
		var pattern=/^\d{10}$/;
		if (!pattern.test(phone)) {
			setError('Ошибка. Неверный формат SMS'+phone+'!'); 
			return false;	
		}
		return true;
	}
	else if (phone_type == 'TRUNK') {
		var pattern=/@/;
		if (!pattern.test(phone)) {
			setError('Ошибка. Неверный формат направления'+phone+'!'); 
			return false;	
		}

		//123@sss
		var offset = phone.indexOf('@');
		offset++;
		var provider = phone.substring(offset, phone.length);
		offset = trunks.indexOf(provider+',');
		if (offset == -1) {
			setError('Ошибка. Нет направления '+provider+' !'); 
			return false;	
		}
		return true;
	}
	else if (phone_type == 'SIP')
		return true
	else if (phone_type == 'DAHDI')
		return true;
	setError('Ошибка. Несуществующий тип '+phone_type+'!'); 
	return false;
}

//добавить абонента в таблице
function addAbonent() {
	var phone = '';
	var phone_type = '';

	//нет проверки на одинаковые основные номера - как сделать ?
	//проверка данных
	//проверка основного номера
	var basePhone = document.getElementById('number1');
	if ((basePhone != null) && (basePhone.value == '')) {
		setError('Ошибка. Основной номер обязателен!');
		return;	
	}
	//проверка дополнительных номеров согласно их приоритета - 1-ый, 2-ой и тд
	//проверка номеров по формату согласно их типа - почтовый, смс и пр
	var isEmpty = false;		
	for (var col=1; col<5; col++) {

		var objPhone = document.getElementById('number'+col);
		if (objPhone == null) {
			setError('Ошибка. Нарушена верстка страницы. Нет поля number'+col);
			return;
		}
		phone = objPhone.value;
		if (isEmpty && (phone != '')) {
				setError('Ошибка. Нарушен порядок дополнительных номеров!'); 
				return;	
		}
		if (phone == '')
			isEmpty = true
		else {
			var objPhoneType = document.getElementById('number_type'+col);
			if (objPhoneType == null) {
				setError('Ошибка. Нарушена верстка страницы. Нет поля number_type'+col);
				return;
			}
			phone_type = objPhoneType.value;
			if (!checkData(phone, phone_type)) 
				return;
		}
	}

	//новая строка данных
	var arr = [];

	for (var col=1; col<5; col++) {
		phone = document.getElementById('number'+col).value;
		phone_type = document.getElementById('number_type'+col).value;
		if (phone != '')
			arr[col-1] = phone_type+':'+phone
		else 
			break;
	}
	
	buffer[buffer.length] = arr;
	showTable();
}

//удалить абонента из таблицы
function deleteAbonent(row) {
	var ind = row-1;
	buffer.splice(ind, 1);
	showTable(); 
}

//отображение данных двумерный массив --> таблица
//генерация html кода для табличного отображения
function htmlTable(data) {

	var str = '<table id=\'call_list_table\' width=\'100%\' cellpadding=\'0\' cellspacing=\'0\' class=\'content\' border=\'0\'>';
	str += '<thead><tr> <th width=\"16\"> </th> <th>Основной номер</th> <th width=\"24%\">Дополнительный номер 1</th> <th width=\"24%\">Дополнительный номер 2</th> <th width=\"24%\">Дополнительный номер 3</th> </tr> </thead><tbody>';

	//строки данных
	var num=0;
	for (var row in data) {
		num = Number(row)+1;

		str += '<tr id=\''+num+'\' class=\'COLOR-'+((num % 2)?1:2)+'\' onClick=\'if(typeof(select_allow) != \"undefined\") if(select_allow == 0) return select_row(this);\'>';

		str += '<td valign=top > <img src=\"../inc/template/blue_style/img/pixel.gif\" alt=\"\" border=\"0\" width=\"1\" height=\"1\">';
		str += '<a href=\"#\" title=\"Удалить\" onclick=\"deleteAbonent('+num+')\"><img src=\"../inc/template/blue_style/img/cut.gif\" border=\"0\" alt=\"del\"></a></td>';

		var col = 0;
		for (col in data[row]) 
			str += '<td><font color=\"black\">'+data[row][col]+'</font></td>';

		col++;
		for (var i=col; i<4; i++)
			str += '<td></td>';
	
		str += '</tr>';
	}

	//строка добавления элемента
	num++;
	str += '<tr id=\''+num+'\' class=\'COLOR-'+((num % 2)?1:2)+'\' onClick=\'if(typeof(select_allow) != \"undefined\") if(select_allow == 0) return select_row(this);\'>';
	str += '<td valign=\"center\"><img width=\"1\" height=\"1\" border=\"0\" alt=\"\" src=\"../inc/template/blue_style/img/pixel.gif\"><a title=\"Добавить\" href=\"#\" onclick=\"addAbonent()\"><img border=\"0\" alt=\"copy\" src=\"../inc/template/blue_style/img/add.gif\"></a></td>';
	for (var col=1; col<5; col++) 
		str += '<td valign=\"top\"><select name=\'number_type'+col+'\' id=\'number_type'+col+'\'><option value=\'SIP\' selected>SIP</option><option value=\'DAHDI\'>DAHDI</option><option value=\'MAIL\'>MAIL</option><option value=\'SMS\'>SMS</option><option value=\'TRUNK\'>TRUNK</option></select><input  type=\'text\' size=\'11\' name=\"number'+col+'\" id=\"number'+col+'\"></td>';

	str += '</tr>';

	str += '</tbody></table>';
	return str;
}

//глобальный двумерный массив, храним данные
//пример правильного формата данных
//var aaa = "SIP:404,MAIL:aaa@mail.ru\nDAHDI:301,SMS:9181010203";
//формат массива : array[0,0]=SIP:404, array[0,1]=MAIL:aaa@mail.ru, array[1,0]=DAHDI:301, array[1,1]=SMS:9181010203 
var buffer = [];
//флаг определяющий отображение - табличное или текстовое
var isTableView;
//сообщение об ошибке
var errMessage = '';
//направления для проверки типа TRUNK в строке через запятую
var trunks = "";

//html код ссылки над областью редактирования списка абонентов
function htmlLink(str) {
	return '<a href=\"#\" onclick=\"changeView()\"><font size=\"3\">'+str+'</font></a>';
}

function htmlError() {
	return '<div id=\"call_list_error\">'+errMessage+'</div>';
}

//отрисовать таблицу
function showTable() {
	document.getElementById("outerform_call_list").innerHTML = htmlLink('Перейти к простому виду')+	htmlTable(buffer)+htmlError();
}

//данные из textarea переносим в массив
function parseData() {
	callList = document.getElementById("call_list_text");
	if (callList == null) {
		setError('Ошибка. Нарушена верстка страницы. Нет поля call_list_text');
		return false;
	}

	buffer = clStrToArray(callList.value);
	//проверка прибывших данных
	for (var row in buffer)
		for (var col in buffer[row]) {
			var phone = buffer[row][col];
			var buf = phone.split(':');
			if (!checkData(buf[1], buf[0])) 
				return false;
		}
	return true;
}

//переключение режимов просмотра списка
function changeView() {
	if (isTableView && (!parseData()))
		return;
	showView();
}

//отображение таблицы или textarea - процедура введена для того, чтобы разнести отображение первый раз и по клику на линке
//когда еще нужно и данные распарсивать во внутренний массив
function showView() {
	if (isTableView) {
		//отображение в виде таблицы
		showTable();
	}
	else {
		//отображение в виде многострочного поля редактирования
		document.getElementById("outerform_call_list").innerHTML = htmlLink('Перейти к табличному виду')+
			'<br><textarea rows=\"20\" cols=\"70\" name=\"call_list_text\" id=\"call_list_text\">'+clArrayToStr(buffer)+'</textarea>'+
			htmlError();
	}

	document.cookie = 'cceTable='+ (isTableView?1:0);	
	isTableView = !isTableView;
}

function setTableView()
{
	var cookie = ' ' + document.cookie;
	var template = ' cceTable=';
	var flag = null;
	if (cookie.length > 1) 
	{
		var offset = cookie.indexOf(template);
		if (offset != -1) {
			offset += template.length;
			flag = cookie.substr(offset, 1);
		}
	}
	if (flag != null)
		isTableView = (flag == 1)?true:false 
	else
		isTableView = true;
}

function saveToHidden() {
	if (isTableView) {
		//как отменить сохранение ??
		if (!parseData())
			return;
		//если было многострочное textarea, то данные из него
		document.getElementById("call_list").value = document.getElementById("call_list_text").value;
	}
	else
		//если была таблица, то данные из массива
		document.getElementById("call_list").value = clArrayToStr(buffer);
}

function setError(error) {
	document.getElementById("call_list_error").innerHTML = error;
}

//начало кода
get_provider_names();  //взять по Ajax названия направлений
setTableView();
callList = document.getElementById("call_list");
if (callList == null) 
	errMessage = 'Ошибка. Нарушена верстка страницы. Нет поля call_list'
else
{
	if (callList.value != '')
		buffer = clStrToArray(callList.value);
	showView();

	if (document.addEventListener)  // проверка существования метода
  		document.getElementById("submit_btn").addEventListener("click", saveToHidden, false)
	else 
		document.getElementById("submit_btn").attachEvent("onclick", saveToHidden);
}


</script>

<?php


$html->html_stop();
?>


