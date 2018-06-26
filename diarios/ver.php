<?php
	require $_SERVER['DOCUMENT_ROOT'] . '/include/utilidades.php';

	session_start();
	if (!isset($_SESSION['logged_user'])) {
		header('Location: /login/index.php');
		exit();
	}

	if (!isset($_GET['date']) || empty($_GET['date']) || format_error_YMD($_GET['date'])) {
		http_response_code(400);
		include($_SERVER['DOCUMENT_ROOT'] . '/include/400.php');		
		exit();
	}
	
	$date_user = $_GET['date'] . '-' . $_SESSION['logged_user'];

	$conn = new PDO('pgsql:host=localhost;dbname=diariodb', 'postgres', '12345');
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	$stmt = $conn->prepare('SELECT * FROM diarios WHERE dateuser=:a');
	$stmt->bindParam(':a', $date_user);

	$stmt->execute();
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	if (empty($result)) {
		http_response_code(404);
		include($_SERVER['DOCUMENT_ROOT'] . '/include/404.php');
		exit();
	}

	$content_db = $result[0]['content'];

	$content_db = openssl_decrypt($content_db, 'AES-128-CBC', $_SESSION['user_md5'], 0, '0000000000000000'); //desencriptar

	$conn = null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
	<title>Ver</title>
	<meta charset="UTF-8">
	<link rel="shortcut icon" type="image/png" href="/res/diarioapp.png"/>
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<link rel="stylesheet" type="text/css" href="/res/bootstrap-3.3.7/dist/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="/res/global.css">
	<link rel="stylesheet" type="text/css" href="style.css">

	<script type="text/javascript" src="/res/jquery-3.3.1.min.js"></script>
	<script type="text/javascript" src="/res/bootstrap-3.3.7/dist/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="/res/autosize.min.js"></script>

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
		<?php include $_SERVER['DOCUMENT_ROOT'] . '/include/encabezado.php'; ?>

		<h1 class="outside">
			<?php echo legible_dateuser($date_user) ?>
		</h1>
		<div class="cuadro">
			<textarea readonly id="ta" class="form-control" rows="4"><?php echo $content_db ?></textarea>
			<a class="btn btn-primary edit-btn" style="margin-top: 10px" href="<?php echo '/diarios/editar.php?date=' . $_GET['date']; ?>">Editar</a>
		</div>

		<!--Footer-->
		<?php include $_SERVER['DOCUMENT_ROOT'] . '/include/footer.php'; ?>
	</div>
</body>
</html>
