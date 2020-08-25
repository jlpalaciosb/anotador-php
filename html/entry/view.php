<?php
	require_once(realpath($_SERVER['DOCUMENT_ROOT'] . '/..') . '/resources/config.php');
	require_once(LIBRARY_PATH . '/database.php');
	require_once(LIBRARY_PATH . '/utilities.php');

	session_start();
	if (!isset($_SESSION['user'])) {
		header('Location: /user/login.php');
		exit();
	}

	if (!isset($_GET['date']) || empty($_GET['date']) || format_error_YMD($_GET['date'])) {
		http_response_code(400);
		include(TEMPLATES_PATH . '/400.php');		
		exit();
	}
	
	$stmt = $GLOBALS['connection']->prepare('SELECT * FROM entries WHERE owner=:o AND date=:d');
	$stmt->bindParam(':o', $_SESSION['user']);
	$stmt->bindParam(':d', $_GET['date']);

	$stmt->execute();
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	if (empty($result)) {
		http_response_code(404);
		include(TEMPLATES_PATH . '/404.php');
		exit();
	}
	$content = decrypt($result[0]['content'], $_SESSION['crypt_key']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
	<title>Ver</title>
	<meta charset="UTF-8">
	<link rel="shortcut icon" type="image/png" href="/assets/img/diarioapp.png"/>
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<link rel="stylesheet" type="text/css" href="/assets/bootstrap-3.3.7/dist/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="/assets/css/global.css">
	<link rel="stylesheet" type="text/css" href="style.css">

	<script type="text/javascript" src="/assets/jquery-3.3.1.min.js"></script>
	<script type="text/javascript" src="/assets/bootstrap-3.3.7/dist/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="/assets/autosize.min.js"></script>

	<script>
		$(document).ready(function(){
			autosize(document.querySelectorAll('textarea'));
			$("#ta").on("keypress",function(e){
				$(".edit-btn").css("animation", "1s mymove infinite");
			});
		});
	</script>
</head>
<body>
	<div class="container">
		<!--Encabezado?-->
		<?php include TEMPLATES_PATH . '/encabezado.php' ?>

		<h1 class="outside">
			<?php echo legible_date($_GET['date']) ?>
		</h1>
		<div class="cuadro">
			<textarea readonly id="ta" class="form-control" rows="4"><?php echo $content ?></textarea>
			<a class="btn btn-primary edit-btn" style="margin-top: 10px" href="<?php echo '/entry/edit.php?date=' . $_GET['date']; ?>">Editar</a>
		</div>

		<!--Footer-->
		<?php include TEMPLATES_PATH . '/footer.php' ?>
	</div>
</body>
</html>
