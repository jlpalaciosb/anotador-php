<?php
	require $_SERVER['DOCUMENT_ROOT'] . '/include/utilidades.php';

	session_start();
	if (!isset($_SESSION['logged_user'])) {
		header('Location: /login/index.php');
		exit();
	}

	
	if (!isset($_GET['date']) || empty($_GET['date']) || format_error_YMD($_GET['date'])) {
		echo 'Error de parametro get';
		exit();
	}

	$date_user = $_GET['date'] . '-' . $_SESSION['logged_user'];

	if (!bd_has($date_user)) {
		echo 'No se encontró el registro en la base de datos';
		exit();
	}
	
	$conn = new PDO('pgsql:host=localhost;dbname=diariodb', 'postgres', '12345');
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	$stmt = $conn->prepare('DELETE FROM diarios WHERE dateuser=:a');
	$stmt->bindParam(':a', $date_user);
	$stmt->execute();
	
	$conn = null;

	echo 'ok';
?>
