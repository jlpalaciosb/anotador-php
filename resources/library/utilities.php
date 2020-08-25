<?php

$meses = array("enero", "febrero", "marzo", "abril", "mayo", "junio", "julio", "agosto", "setiembre", "octubre", "noviembre", "diciembre");

#Recibe = YYYY-MM
#Retorna = mes AÑO, ejemplo = mayo 2018
function legible_YM ($year_month) {
	return $GLOBALS["meses"][intval(substr($year_month, 5)) - 1] . " / " . substr($year_month, 0, 4);
}

#retorna la fecha en un formato más legible
function legible_YMD ($anho, $mes, $dia) {
	$str  = "";
	if ($dia < 10) $str = $str . "0";
	$str = $str . strval($dia) . " / ";
	$str = $str . $GLOBALS["meses"][$mes - 1] . " / ";
	$str = $str . strval($anho);
	return $str;
}

#retorna la fecha en un formato más legible de $dateuser
function legible_date ($date) {
	$anho = intval(substr($date, 0, 4));
	$mes = intval(substr($date, 5, 2));
	$dia = intval(substr($date, 8, 2));
	return legible_YMD($anho, $mes, $dia);
}

#Retorna true si el string recibido $YM no es del formato YYYY-MM
function format_error_YM ($ym) {
	if (strlen($ym) != 7)
		return true;

	if (substr($ym, 4, 1) != "-")
		return true;

	$year = substr($ym, 0, 4);
	if (!ctype_digit($year) || intval($year) < 2000 || intval($year) > 2100)
		return true;

	$month = substr($ym, 5, 2);
	if (!ctype_digit($month) || intval($month) < 1 || intval($month) > 12)
		return true;

	return false;
}

#Retorna true si el string recibido $YMD no es del formato YYYY-MM-DD
function format_error_YMD ($ymd) {
	if (strlen($ymd) != 10)
		return true;

	if (substr($ymd, 4, 1) != '-' || substr($ymd, 7, 1) != '-')
		return true;

	$year = substr($ymd, 0, 4);
	if (!ctype_digit($year) || intval($year) < 2000 || intval($year) > 2100)
		return true;

	$month = substr($ymd, 5, 2);
	if (!ctype_digit($month) || intval($month) < 1 || intval($month) > 12)
		return true;

	$day = substr($ymd, 8, 2);
	if (!ctype_digit($day) || intval($day) < 1 || intval($day) > 31)
		return true;

	return false;
}

#retorna un string en el formato de YYYY-MM correspondiente al mes anterior de $YEAR_MONTH
function mes_anterior($year_month) {
	$y = intval(substr($year_month, 0, 4));
	$m = intval(substr($year_month, 5));
	$m = $m - 1;
	if($m == 0){
		$m = 12;
		$y = $y - 1;
	}
	$s = "";
	if ($m < 10) $s = "0";
	return strval($y) . "-" . $s . strval($m);
}

#retorna un string en el formato de YYYY-MM correspondiente al mes siguiente de $YEAR_MONTH
function mes_siguiente($year_month) {
	$y = intval(substr($year_month, 0, 4));
	$m = intval(substr($year_month, 5));
	$m = $m + 1;
	if($m == 13){
		$m = 1;
		$y = $y + 1;
	}
	$s = "";
	if ($m < 10) $s = "0";
	return strval($y) . "-" . $s . strval($m);
}

#retorna la fecha actual en el formato de fecha que usamos
function get_current_date() {
	$ret = date("Y") . "-";
	$ret = $ret . date("m") . "-";
	$ret = $ret . date("d");
	return $ret;
}

#retorna dateuser con la fecha recibida
function to_string_YMD($anho, $mes, $dia) {
	$str  = "";
	$str = $str . strval($anho) . "-";
	if ($mes < 10) $str = $str . "0";
	$str = $str . strval($mes) . "-";
	if ($dia < 10) $str = $str . "0";
	$str = $str . strval($dia);
	return $str;
}

function encrypt($content, $key) {
	return openssl_encrypt($content, 'AES-128-CBC', $key, 0, '0000000000000000');
}

function decrypt($crypted, $key) {
	return openssl_decrypt($crypted, 'AES-128-CBC', $key, 0, '0000000000000000');
}
