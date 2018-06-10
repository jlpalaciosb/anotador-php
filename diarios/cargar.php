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
	
	if (!bd_has($date_user)) {
		$conn = new PDO('pgsql:host=localhost;dbname=diariodb', 'postgres', '12345');
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		$stmt = $conn->prepare('INSERT INTO diarios (dateuser,owner,content) VALUES (:a,:b,:c)');
		$content = openssl_encrypt('', 'AES-128-CBC', $_SESSION['user_md5'], 0, '0000000000000000'); //encriptamos una cadena vacia '' que es el contenido inicial de un diario

		$stmt->bindParam(':a', $date_user);
		$stmt->bindParam(':b', $_SESSION['logged_user']);
		$stmt->bindParam(':c', $content);
		
		$stmt->execute();
		$conn = null;
	}
	header('Location: /diarios/editar.php?date=' . $_GET['date']);
?>
