<?php
	require $_SERVER["DOCUMENT_ROOT"] . "/include/utilidades.php";

	session_start();
	if (!isset($_SESSION["diario_user_logged"])) {
		header("Location: /login/index.php");
		exit();
	}
	if (!isset($_GET["date"]) || format_error_YM($_GET["date"])) {
		header("Location: /index.php?date=" . date("Y") . "-" . date("m"));
		exit();
	}

?>

<!DOCTYPE html>
<html lang="es">
<head>
	<title>Diario</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="/bootstrap-3.3.7/dist/css/bootstrap.css">
	<link rel="stylesheet" href="/style.css">
	<link rel="shortcut icon" type="image/png" href="/res/diarioapp.png"/>
	<meta charset="UTF-8">
</head>
<body>
	<div class="container">
		<!--Encabezado?-->
		<?php include $_SERVER["DOCUMENT_ROOT"] . "/include/encabezado.php"; ?>

		<!--Navegador de Meses-->
		<center><div class="cuadro" style="text-align: center; max-width: 500px;">
			<a class="btn btn-default" href="/index.php?date=<?php echo mes_anterior($_GET['date'])?>">
				<?php echo substr($GLOBALS["meses"][intval(substr(mes_anterior($_GET['date']), 5)) - 1], 0 , 3)?>
			</a>
			<label style="margin-left: 10px; margin-right: 10px;">
				<b><?php echo legible_YM($_GET["date"]); ?></b>		
			</label>
			<a class="btn btn-default" href="/index.php?date=<?php echo mes_siguiente($_GET['date'])?>">
				<?php echo substr($GLOBALS["meses"][intval(substr(mes_siguiente($_GET['date']), 5)) - 1], 0 , 3)?>
			</a>
		</div></center>
		<!--Fin del Complejo Navegador de Meses (ok no!)-->

		<!--Título de la lista-->
		<div style="margin-bottom: 10px;">
			<h1 class="outside floated" style="margin: 0 0 0 0;">
				Tus diarios de <?php echo legible_YM($_GET["date"]); ?>
			</h1>
			<a href="/diarios/cargar.php?date=<?php echo substr(get_dateuser(),0,10) ?>" style="float: right;">
	 			<img src="/res/add.png" title="Carga tu diario de hoy" style="height:42px;border:0;">
			</a>
			<div class="clearman"></div>
		</div>
		<!--End of Título de la lista-->

		<!--Comienzo de la Lista-->
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
	#imprime una fila de la tabla
	function imprimirFila($anho, $mes, $dia) {
		$dateuser = get_dateuser_fecha($anho, $mes, $dia);

		$justDate = substr($dateuser, 0, 10);

		echo "<tr>";

		echo "<td>";
			if (cargado_en_bd($dateuser)) {
				echo "<span class=\"glyphicon glyphicon-file\"></span>\n";
				echo "<a href=\"/diarios/ver.php?date=" . $justDate . "\">";
					echo legible_YMD($anho, $mes, $dia);
				echo "</a>\n";
			} else {
				echo legible_YMD($anho, $mes, $dia);
			}
		echo "</td>\n";

		echo "<td class=\"text-right text-nowrap\">";
			if (cargado_en_bd($dateuser)) {
				echo "<a href=\"/diarios/editar.php?date=" . $justDate . "\">";
					echo "<button class=\"btn btn-xs btn-info\">Editar</button>";
				echo "</a>\n";

				$href = "/diarios/eliminar.php?date=" . $justDate . "&return=" . $_SERVER['REQUEST_URI'];
				echo "<a href=\"" . $href . "\">";
					echo "<button class=\"btn btn-xs btn-warning\">";
						echo "<span class=\"glyphicon glyphicon-trash\"></span>";
					echo "</button>";
				echo "</a>\n";
			} else {
				echo "<a href=\"/diarios/cargar.php?date=" . $justDate . "\">";
					echo "<button class=\"btn btn-xs btn-info\">Cargar</button>\n";
				echo "</a>\n";
			}
		echo "</td>\n";

		echo "</tr>\n";
	}
?>
