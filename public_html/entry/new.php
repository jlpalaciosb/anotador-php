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
	
	if (!exists_entry($_SESSION['user'], $_GET['date'])) {
		$stmt = $GLOBALS['connection']->prepare('INSERT INTO entries (owner,date,content) VALUES (:o,:d,:c)');
		$stmt->bindParam(':o', $_SESSION['user']);
		$stmt->bindParam(':d', $_GET['date']);
		$content = encrypt('', $_SESSION['crypt_key']);
		$stmt->bindParam(':c', $content);
		$stmt->execute();
	}
	
	header('Location: /entry/edit.php?date=' . $_GET['date']);
?>
