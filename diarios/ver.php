<?php
	require $_SERVER['DOCUMENT_ROOT'] . '/include/utilidades.php';

	session_start();
	if (!isset($_SESSION['logged_user'])) {
		header('Location: /login/index.php');
		exit();
	}

	if (!isset($_GET['date']) || empty($_GET['date'])) {
		echo 'Error de parametro get';
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
		echo 'No se encontró registro en la base de datos';
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
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="/bootstrap-3.3.7/dist/css/bootstrap.css">
	<link rel="stylesheet" href="/style.css">
	<link rel="shortcut icon" type="image/png" href="/res/diarioapp.png"/>
	<meta charset="UTF-8">
	<script>
		var textarea = null;
		window.addEventListener("load", function() {
			textarea = window.document.querySelector("textarea");
			textarea.style.height = textarea.scrollHeight + 2 + "px";
		}, false);
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
			<textarea readonly class="form-control"><?php echo $content_db ?></textarea>
			<a class="btn btn-primary" style="margin-top: 10px" href="<?php echo '/diarios/editar.php?date=' . $_GET['date']; ?>">Editar</a>
		</div>
	</div>
</body>
</html>
