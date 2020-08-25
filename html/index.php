<?php
	require_once(realpath($_SERVER['DOCUMENT_ROOT'] . '/..') . '/resources/config.php');
	require_once(LIBRARY_PATH . '/database.php');
	require_once(LIBRARY_PATH . '/utilities.php');

	session_start();
	if (!isset($_SESSION['user'])) {
		header('Location: /user/login.php');
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
	<title>Anotador</title>
	<meta charset="UTF-8">
	<link rel="shortcut icon" type="image/png" href="/assets/img/diarioapp.png"/>
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<link rel="stylesheet" type="text/css" href="/assets/bootstrap-3.3.7/dist/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="/assets/css/global.css">
	<link rel="stylesheet" type="text/css" href="/assets/css/loading.css">
	<link rel="stylesheet" type="text/css" href="style.css">

	<script type="text/javascript" src="/assets/jquery-3.3.1.min.js"></script>
	<script type="text/javascript" src="/assets/bootstrap-3.3.7/dist/js/bootstrap.min.js"></script>
	
	<!-- Control modal eliminar -->
	<script type="text/javascript">
		var a_eliminar = 'initial';
		var id_eliminar = -1;
		var modal_body0 = '<p>¿Seguro que quieres eliminar tus notas del <span id="span"></span>?</p>';
		var modal_body1 = '<p>Eliminando</p><div class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>';
		var modal_body2 = '<p>Eliminado</p>';
		var modal_footer0 = '<button type="button" class="btn btn-danger" onclick="eliminar()">Sí</button> <button type="button" class="btn btn-default" data-dismiss="modal">No</button>';
		var modal_footer1 = '';
		var modal_footer2 = '<button type="button" class="btn btn-success" data-dismiss="modal">Ok</button>';
		var navbar;
		var sticky;
		function on_show_modal() {
			$("#modal-body").html(modal_body0);
			$("#span").html(legibleYMD(a_eliminar));
			$("#modal-footer").html(modal_footer0);
		}
		function eliminar() {
			$("#modal-body").html(modal_body1);
			$("#modal-footer").html(modal_footer1);
			var xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function() {
				if (this.readyState === 4) {
					if (this.status === 200 && this.responseText === "ok") {
						setTimeout(eliminado, 1000);
					} else {
						alert("error");
					}
				}
			};
			xhttp.open("GET", "/entry/delete.php?date=" + a_eliminar, true);
			xhttp.send();
		}
		async function eliminado() {
			$("#modal-body").html(modal_body2);
			$("#modal-footer").html(modal_footer2);
			var content = '';
			content += '<td>';
			content +=     legibleYMD(a_eliminar);
			content += '</td>';
			content += '<td class="text-right text-nowrap">';
			content +=     '<a href="/entry/new.php?date=' + a_eliminar + '">';
			content +=         '<button class="btn btn-xs btn-info">Escribir</button> ';
			content +=     '</a> ';
			content += '</td> ';
			$("#"+id_eliminar).html(content);
		}
		function legibleYMD(ymd) {
			var months = ["enero", "febrero", "marzo", "abril", "mayo", "junio", "julio", "agosto", "setiembre", "octubre", "noviembre", "diciembre"];
			var year = ymd.substr(0,4);
			var month = ymd.substr(5,2);
			var day = ymd.substr(8,2);
			return day + " - " + months[parseInt(month)-1] + " - " + year;
		}
		$(document).ready(function(){
			
		});
	</script>
</head>
<body>
	<div class="container" style="">
		<!--Encabezado?-->
		<?php include(TEMPLATES_PATH . '/encabezado.php'); ?>

		<!--Navegador de Meses-->
		<center><div class="cuadro" style="text-align: center; max-width: 350px; margin-top: 15px;">
			<a class="btn btn-default" href="/index.php?date=<?php echo mes_anterior($_GET['date'])?>" style="width: 60px;">
				<?php echo substr($GLOBALS['meses'][intval(substr(mes_anterior($_GET['date']), 5)) - 1], 0 , 3) ?>
			</a>
			<label style="margin-left: 10px; margin-right: 10px;">
				<b><?php echo legible_YM($_GET['date']) ?></b>		
			</label>
			<a class="btn btn-default" href="/index.php?date=<?php echo mes_siguiente($_GET['date'])?>" style="width: 60px;">
				<?php echo substr($GLOBALS["meses"][intval(substr(mes_siguiente($_GET['date']), 5)) - 1], 0 , 3) ?>
			</a>
		</div></center>
		<!--Fin del Complejo Navegador de Meses, ok no->

		<!--Título de la lista-->
		<div id="listTitle">
			<h1 class="outside">
				Notas de <?php echo legible_YM($_GET['date']) ?>
			</h1>
			<a href="/entry/new.php?date=<?php echo get_current_date() ?>">
	 			<img src="/assets/img/add.png" title="Escribe tus notas de hoy">
			</a>
			<div class="clearman"></div>
		</div>
		<!--End of Título de la lista-->

		<!--Lista-->
		<div class="panel panel-default cuadro" id="panel">
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
				<div class="modal-content" style="background-color: lightgrey; padding: 10px 0px 5px 0px; border-radius: 5px;">
					<div class="modal-header" style="background-color: lightgrey;">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">Eliminar notas de esta fecha</h4>
					</div>
					<div class="modal-body" id="modal-body" style="background-color: grey;color: white;"></div>
					<div class="modal-footer" id="modal-footer" style="background-color: lightgrey;"></div>
				</div>
			</div>
		</div>

		<!--Footer-->
		<?php include(TEMPLATES_PATH . '/footer.php'); ?>
	</div>
</body>
</html>

<?php
	#imprime una fila de la tabla
	function imprimirFila($anho, $mes, $dia) {
		$date = to_string_YMD($anho, $mes, $dia);

		echo '<tr id='. $dia . ' >';

		echo '<td>';
			if (exists_entry($_SESSION['user'], $date)) {
				echo '<span class="glyphicon glyphicon-file"></span>' . "\n";
				echo '<a href="/entry/view.php?date=' . $date . '">';
					echo legible_YMD($anho, $mes, $dia);
				echo '</a>' . "\n";
			} else {
				echo legible_YMD($anho, $mes, $dia);
			}
		echo '</td>' . "\n";

		echo '<td class="text-right text-nowrap">';
			if (exists_entry($_SESSION['user'], $date)) {
				echo '<a href="/entry/edit.php?date=' . $date . '">';
					echo '<button class="btn btn-xs btn-info">Escribir</button>';
				echo '</a>' . "\n";

				echo '<button class="btn btn-xs btn-warning" onclick="a_eliminar=\'' . $date . '\';id_eliminar=' . $dia . ';on_show_modal();" data-toggle="modal" data-target="#myModal">';
					echo '<span class="glyphicon glyphicon-trash"></span>';
				echo '</button>';
			} else {
				echo '<a href="/entry/new.php?date=' . $date . '">';
					echo '<button class="btn btn-xs btn-info">Escribir</button>' . "\n";
				echo '</a>' . "\n";
			}
		echo '</td>' . "\n";

		echo '</tr>' . "\n";
	}
?>
