<?php
	$meses = array("enero", "febrero", "marzo", "abril", "mayo", "junio", "julio", "agosto", "setiembre", "octubre", "noviembre", "diciembre");
	
	#Recibe = YYYY-MM
	#Retorna = mes AÑO, ejemplo = mayo 2018
	function legible_YM ($year_month) {
		return $GLOBALS["meses"][intval(substr($year_month, 5)) - 1] . " " . substr($year_month, 0, 4);
	}

	#retorna la fecha en un formato más legible
	function legible_YMD ($anho, $mes, $dia) {
		$str  = "";
		if ($dia < 10) $str = $str . "0";
		$str = $str . strval($dia) . " - ";
		$str = $str . $GLOBALS["meses"][$mes - 1] . " - ";
		$str = $str . strval($anho);
		return $str;
	}

	#retorna la fecha en un formato más legible de $dateuser
	function legible_dateuser ($dateuser) {
		$anho = intval(substr($dateuser, 0, 4));
		$mes = intval(substr($dateuser, 5, 2));
		$dia = intval(substr($dateuser, 8, 2));
		return legible_YMD($anho, $mes, $dia);
	}

	#Retorna true si el string recibido $YEAR_MONTH no es del formato YYYY-MM
	function format_error_YM ($year_month) {
		if (strlen($year_month) != 7)
			return true;
		if (substr($year_month, 4, 1) != "-")
			return true;
		if (intval(substr($year_month, 0, 4)) < 2000 || intval(substr($year_month, 0, 4)) > 2100)
			return true;
		if (intval(substr($year_month, 5)) < 1 || intval(substr($year_month, 5) > 12))
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

	#retorna dateuser con la fecha actual
	function get_dateuser() {
		$ret = date("Y") . "-";
		$ret = $ret . date("m") . "-";
		$ret = $ret . date("d") . "-";
		$ret = $ret . $_SESSION["logged_user"];
		return $ret;
	}

	#retorna dateuser con la fecha recibida
	function get_dateuser_fecha($anho, $mes, $dia) {
		$str  = "";
		$str = $str . strval($anho) . "-";
		if ($mes < 10) $str = $str . "0";
		$str = $str . strval($mes) . "-";
		if ($dia < 10) $str = $str . "0";
		$str = $str . strval($dia) . "-";
		$str = $str . $_SESSION["logged_user"];
		return $str;
	}

	#Retorna true si ya se cargó el diario con clave $DATEUSER en la bd
	function cargado_en_bd ($dateuser) {
		$cargado = true;

		$conn = new PDO("pgsql:host=localhost;dbname=diariodb", "postgres", "12345");
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		$stmt = $conn->prepare("SELECT * FROM diarios WHERE dateuser=:a");
		$stmt->bindParam(':a', $dateuser);

		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if (empty($result))
			$cargado = false;

		$conn = null;

		return $cargado;
	}

	/* Retorna true si en la base de datos ya hay un registro (en la tabla diarios) con $DATEUSER como clave*/
	function bd_has($date_user) {
		$cargado = true;

		$conn = new PDO('pgsql:host=localhost;dbname=diariodb', 'postgres', '12345');
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		$stmt = $conn->prepare('SELECT * FROM diarios WHERE dateuser=:a');
		$stmt->bindParam(':a', $date_user);

		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if (empty($result))
			$cargado = false;

		$conn = null;

		return $cargado;
	}

	function registered($username) {
		$registrado = true;
	    try {
    		$conn = new PDO('pgsql:host=localhost;dbname=diariodb', 'postgres', '12345');
    		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    		
    		$stmt = $conn->prepare('SELECT * FROM users WHERE username=:a');
    		$stmt->bindParam(':a', $username);
			$stmt->execute();

    		if ($stmt->rowCount() == 0) {
    			$registrado = false;
    		}
			$conn = null;
		} catch(Exception $ex) {
			echo '<p class="error">Error: ' . $ex->getMessage() . '</p>';
			exit();
		}
		return $registrado;
	}

	function register($username, $password) {
		try {
    		$conn = new PDO('pgsql:host=localhost;dbname=diariodb', 'postgres', '12345');
    		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    		
    		$stmt = $conn->prepare('INSERT INTO users (username, password_md5) VALUES (:a, :b)');
    		$stmt->bindParam(':a', $username);
    		$stmt->bindParam(':b', $password);

    		$password = md5($password);

    		$stmt->execute();
    		$conn = null;
		} catch(Exception $ex) {
			echo '<p class="error">Error: ' . $ex->getMessage() . '</p>';
			exit();
		}
		return true;
	}

	function authenticate($username, $password) {
		$correcto = false;
		try {
    		$conn = new PDO('pgsql:host=localhost;dbname=diariodb', 'postgres', '12345');
    		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    		
    		$stmt = $conn->prepare('SELECT * FROM users WHERE username=:a AND password_md5=:b');
    		$stmt->bindParam(':a', $username);
    		$stmt->bindParam(':b', $password);

    		$password = md5($password);
    		$stmt->execute();

    		if ($stmt->rowCount() == 1) {
    			$correcto = true;
    		}
		} catch(Exception $ex) {
			echo '<p class="error">Error: ' . $ex->getMessage() . '</p>';
			exit();
		}
		return $correcto;
	}
?>
