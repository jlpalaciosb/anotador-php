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

	if (!bd_has($date_user)) {
		http_response_code(404);
		include($_SERVER['DOCUMENT_ROOT'] . '/include/404.php');		
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
