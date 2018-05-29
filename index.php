<?php
	session_start();
	if (!isset($_SESSION["diario_user_logged"])) {
		header("Location: /login/index.php");
		exit();
	}
	if (!isset($_GET["date"]) || format_error($_GET["date"])) {
		header("Location: /index.php?date=" . date("Y") . "-" . date("m"));
		exit();
	}
	$meses = array("enero", "febrero", "marzo", "abril", "mayo", "junio", "julio", "agosto", "setiembre", "octubre", "noviembre", "diciembre");
?>

<!DOCTYPE html>
<html lang="es">
<head>
	<title>Diario</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="/bootstrap-3.3.7/dist/css/bootstrap.css">
	<link rel="stylesheet" href="/style.css">
	<meta charset="UTF-8">
</head>
<body>
	<div class="container">
		<!--Encabezado?-->
		<img id="logo" src="/res/diarioapp2.png">
		<div id="account" class="cuadro">
			<span>( <?php echo $_SESSION["diario_user_logged"]; ?> )</span>
			<a href="/login/logout.php">Salir</a>
		</div>
		<div class="clearman"></div>

		<!--Navegador de Meses-->
		<center><div class="cuadro" style="text-align: center; max-width: 500px;">
			<a class="btn btn-default" href="/index.php?date=<?php echo mes_anterior($_GET['date'])?>">
				<?php echo substr($GLOBALS["meses"][intval(substr(mes_anterior($_GET['date']), 5)) - 1], 0 , 3)?>
			</a>
			<label style="margin-left: 10px; margin-right: 10px;">
				<b><?php echo lindo($_GET["date"]); ?></b>		
			</label>
			<a class="btn btn-default" href="/index.php?date=<?php echo mes_siguiente($_GET['date'])?>">
				<?php echo substr($GLOBALS["meses"][intval(substr(mes_siguiente($_GET['date']), 5)) - 1], 0 , 3)?>
			</a>
		</div></center>
		<!--Fin del Complejo Navegador de Meses (ok no!)-->

		<!--Título de la lista-->
		<div style="margin-bottom: 10px;">
			<h1 class="outside floated" style="margin: 0 0 0 0;">
				Tus diarios de <?php echo lindo($_GET["date"]); ?>
			</h1>
			<a href="/diarios/cargar.php?dateuser=<?php echo get_dateuser() ?>" style="float: right;">
	 			<img src="/res/add.png" title="Carga tu diario de hoy" style="height:42px;border:0;">
			</a>
			<div class="clearman"></div>
		</div>
		<!--End of Título de la lista-->

		<!--COMIENZO de la Lista-->
		<div class="panel panel-default cuadro">
			<table class="table table-hover">
				<tbody>
					<?php
						$anho = intval(substr($_GET["date"], 0, 4));
						$mes = intval(substr($_GET["date"], 5));
						for ($i=1; $i <= cal_days_in_month(CAL_GREGORIAN, $mes, $anho); $i++) {
							imprimirFila($anho, $mes, $i);
						}
					?>
				</tbody>
			</table>
		</div>
		<!--Fin de la Lista-->

		<p class="outside">in the end</p>
	</div>
</body>
</html>

<?php

	#Imprime la fecha $year_month en un formato más legible
	function lindo ($year_month) {
		return $GLOBALS["meses"][intval(substr($year_month, 5)) - 1] . " " . substr($year_month, 0, 4);
	}

	#Retorna true si el string recibido $YEAR_MONTH no es del formato YYYY-MM
	function format_error ($year_month) {
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

	#retorna dateuser
	function get_dateuser() {
		$ret = date("Y") . "-";
		$ret = $ret . date("m") . "-";
		$ret = $ret . date("d") . "-";
		$ret = $ret . $_SESSION["diario_user_logged"];
		return $ret;
	}

	#retorna dateuser formateado
	function get_dateuser2($anho, $mes, $dia) {
		$str  = "";
		$str = $str . strval($anho) . "-";
		if ($mes < 10) $str = $str . "0";
		$str = $str . strval($mes) . "-";
		if ($dia < 10) $str = $str . "0";
		$str = $str . strval($dia) . "-";
		$str = $str . $_SESSION["diario_user_logged"];
		return $str;
	}

	#imprime una fila de la tabla
	function imprimirFila($anho, $mes, $dia) {
		$dateuser = get_dateuser2($anho, $mes, $dia);

		echo "<tr>";

		echo "<td>";
			if (yaCargado($dateuser)) {
				echo "<span class=\"glyphicon glyphicon-file\"></span>\n";
				echo "<a href=\"/diarios/ver.php?dateuser=" . $dateuser . "\">";
					echo lindo2($anho, $mes, $dia);
				echo "</a>\n";
			} else {
				echo lindo2($anho, $mes, $dia);
			}
		echo "</td>\n";

		echo "<td class=\"text-right text-nowrap\">";
			if (yaCargado($dateuser)) {
				echo "<a href=\"/diarios/editar.php?dateuser=" . $dateuser . "\">";
					echo "<button class=\"btn btn-xs btn-info\">Editar</button>";
				echo "</a>\n";
				echo "<a href=\"/diarios/eliminar.php?dateuser=" . $dateuser . "\">";
					echo "<button class=\"btn btn-xs btn-warning\">";
						echo "<span class=\"glyphicon glyphicon-trash\"></span>";
					echo "</button>";
				echo "</a>\n";
			} else {
				echo "<a href=\"/diarios/cargar.php?dateuser=" . $dateuser . "\">";
					echo "<button class=\"btn btn-xs btn-info\">Cargar</button>\n";
				echo "</a>\n";
			}
		echo "</td>\n";

		echo "</tr>\n";
	}

	/* <tr>
		<td>
			<span class="glyphicon glyphicon-file"></span>
			<a href="ver">24 - mayo - 2018</a>
		</td>
		<td class="text-right text-nowrap">
			<a href="editar"><button class="btn btn-xs btn-info">Editar</button></a>
			<a href="eliminar"><button class="btn btn-xs btn-warning">
				<span class="glyphicon glyphicon-trash"></span>
			</button></a>
		</td>
	</tr> */

	function yaCargado($dateuser) {
		$cargado = true;

		$conn = null;
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

	#retorna la fecha $year_month_day en un formato más legible
	function lindo2 ($anho, $mes, $dia) {
		$str  = "";
		if ($dia < 10) $str = $str . "0";
		$str = $str . strval($dia) . " - ";
		$str = $str . $GLOBALS["meses"][$mes - 1] . " - ";
		$str = $str . strval($anho);
		return $str;
	}
?>