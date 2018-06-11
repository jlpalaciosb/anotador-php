<?php
	require $_SERVER['DOCUMENT_ROOT'] . '/include/utilidades.php';

	session_start();
	if (!isset($_SESSION['logged_user'])) {
		header('Location: /login/index.php');
		exit();
	}

	if (!isset($_GET['date']) || format_error_YM($_GET['date'])) {
		header('Location: /index.php?date=' . date('Y') . '-' . date('m'));
		exit();
	}
?>

<!DOCTYPE html>
<html lang="es">
<head>
	<title>Diario</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="/bootstrap-3.3.7/dist/css/bootstrap.min.css">
	<script src="/bootstrap-3.3.7/js/tests/vendor/jquery.min.js"></script>
	<script src="/bootstrap-3.3.7/dist/js/bootstrap.min.js"></script>
	<link rel="shortcut icon" type="image/png" href="/res/diarioapp.png"/>
	<link rel="stylesheet" href="/style.css">
	<link rel="stylesheet" href="/loading.css">
	<meta charset="UTF-8">
	<script type="text/javascript">
		var a_eliminar = 'initial';
		var id_eliminar = -1;
		var modal_body0 = '<p>Estás segur@ de que quieres eliminar tu diario del <span id="span"></span></p>';
		var modal_body1 = '<p>Eliminando</p><div class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>';
		var modal_body2 = '<p>Eliminado</p>';
		var modal_footer0 = '<button type="button" class="btn btn-danger" onclick="eliminar()">Sí</button> <button type="button" class="btn btn-default" data-dismiss="modal">No</button>';
		var modal_footer1 = '';
		var modal_footer2 = '<button type="button" class="btn btn-success" data-dismiss="modal">Ok</button>';
		function on_show_modal() {
			document.getElementById("modal-body").innerHTML = modal_body0;
			document.getElementById("span").innerHTML = legibleYMD(a_eliminar);
			document.getElementById("modal-footer").innerHTML = modal_footer0;
		}
		function eliminar() {
			document.getElementById("modal-body").innerHTML = modal_body1;
			document.getElementById("modal-footer").innerHTML = modal_footer1;
			var xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function() {
				if (this.readyState == 4 && this.status == 200) {
					if (this.responseText == "ok") {
						setTimeout(eliminado, 1000);
					} else {
						alert("error");
					}
				}
			};
			xhttp.open("GET", "/diarios/eliminar.php?date=" + a_eliminar, true);
			xhttp.send();
		}
		async function eliminado() {
			document.getElementById("modal-body").innerHTML = modal_body2;
			document.getElementById("modal-footer").innerHTML = modal_footer2;
			var content = '';
			content += '<td>';
			content +=     legibleYMD(a_eliminar);
			content += '</td>';
			content += '<td class="text-right text-nowrap">';
			content +=     '<a href="/diarios/cargar.php?date=' + a_eliminar + '">';
			content +=         '<button class="btn btn-xs btn-info">Cargar</button> ';
			content +=     '</a> ';
			content += '</td> ';
			document.getElementById(id_eliminar).innerHTML = content;
		}
		function legibleYMD(ymd) {
			var months = ["enero", "febrero", "marzo", "abril", "mayo", "junio", "julio", "agosto", "setiembre", "octubre", "noviembre", "diciembre"];
			var year = ymd.substr(0,4);
			var month = ymd.substr(5,2);
			var day = ymd.substr(8,2);
			return day + " - " + months[parseInt(month)-1] + " - " + year;
		}
	</script>
</head>
<body>
	<div class="container">
		<!--Encabezado?-->
		<?php include $_SERVER['DOCUMENT_ROOT'] . '/include/encabezado.php'; ?>

		<!--Navegador de Meses-->
		<center><div class="cuadro" style="text-align: center; max-width: 350px;">
			<a class="btn btn-default" href="/index.php?date=<?php echo mes_anterior($_GET['date'])?>">
				<?php echo substr($GLOBALS['meses'][intval(substr(mes_anterior($_GET['date']), 5)) - 1], 0 , 3) ?>
			</a>
			<label style="margin-left: 10px; margin-right: 10px;">
				<b><?php echo legible_YM($_GET['date']) ?></b>		
			</label>
			<a class="btn btn-default" href="/index.php?date=<?php echo mes_siguiente($_GET['date'])?>">
				<?php echo substr($GLOBALS["meses"][intval(substr(mes_siguiente($_GET['date']), 5)) - 1], 0 , 3) ?>
			</a>
		</div></center>
		<!--Fin del Complejo Navegador de Meses (ok no!)-->

		<!--Título de la lista-->
		<div style="margin-bottom: 10px;">
			<h1 class="outside floated" style="margin: 0 0 0 0;">
				Tus diarios de <?php echo legible_YM($_GET['date']) ?>
			</h1>
			<a href="/diarios/cargar.php?date=<?php echo substr(get_dateuser(),0,10) ?>" style="float: right;">
	 			<img src="/res/add.png" title="Carga tu diario de hoy" style="height:42px;border:0;">
			</a>
			<div class="clearman"></div>
		</div>
		<!--End of Título de la lista-->

		<!--Lista-->
		<div class="panel panel-default cuadro">
			<table class="table table-hover">
				<tbody>
					<?php
						$anho = intval(substr($_GET['date'], 0, 4));
						$mes = intval(substr($_GET['date'], 5));
						for ($i=1; $i <= cal_days_in_month(CAL_GREGORIAN, $mes, $anho); $i++) {
							imprimirFila($anho, $mes, $i);
						}
					?>
				</tbody>
			</table>
		</div>

		<!-- Modal de eliminar-->
		<div class="modal fade" id="myModal" role="dialog">
			<div class="modal-dialog">
				<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">Eliminar diario</h4>
					</div>
					<div class="modal-body" id="modal-body" style="background-color: #3a595f;color: white;"></div>
					<div class="modal-footer" id="modal-footer"></div>
				</div>
			</div>
		</div>

	</div>
</body>
</html>

<?php
	#imprime una fila de la tabla
	function imprimirFila($anho, $mes, $dia) {
		$date_user = get_dateuser_fecha($anho, $mes, $dia);
		$just_date = substr($date_user, 0, 10);

		echo '<tr id='. $dia . ' >';

		echo '<td>';
			if (bd_has($date_user)) {
				echo '<span class="glyphicon glyphicon-file"></span>' . "\n";
				echo '<a href="/diarios/ver.php?date=' . $just_date . '">';
					echo legible_YMD($anho, $mes, $dia);
				echo '</a>' . "\n";
			} else {
				echo legible_YMD($anho, $mes, $dia);
			}
		echo '</td>' . "\n";

		echo '<td class="text-right text-nowrap">';
			if (bd_has($date_user)) {
				echo '<a href="/diarios/editar.php?date=' . $just_date . '">';
					echo '<button class="btn btn-xs btn-info">Editar</button>';
				echo '</a>' . "\n";

				$href = '/diarios/eliminar.php?date=' . $just_date . '&return=' . $_SERVER['REQUEST_URI'];
				echo '<button class="btn btn-xs btn-warning" onclick="a_eliminar=\'' . $just_date . '\';id_eliminar=' . $dia . ';on_show_modal();" data-toggle="modal" data-target="#myModal">';
					echo '<span class="glyphicon glyphicon-trash"></span>';
				echo '</button>';
			} else {
				echo '<a href="/diarios/cargar.php?date=' . $just_date . '">';
					echo '<button class="btn btn-xs btn-info">Cargar</button>' . "\n";
				echo '</a>' . "\n";
			}
		echo '</td>' . "\n";

		echo '</tr>' . "\n";
	}
?>
