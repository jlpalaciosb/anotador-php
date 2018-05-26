<?php
	session_start();

	if (!isset($_SESSION['diario_user_logged'])) {
		header("Location: /diario/login/index.php");
		exit();
	}

	$dateuserPar = "";
	try {
		if (!isset($_GET["dateuser"]) || empty($_GET["dateuser"]))
			throw new Exception("Error de parametro get");

		$dateuserPar = $_GET["dateuser"];

		if (yaCargado($dateuserPar)) {
			if(!tengoPermiso($dateuserPar))
				throw new Exception("No tienes permiso");
			$conn = null;
			$conn = new PDO("pgsql:host=localhost;dbname=diariodb", "postgres", "12345");
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			$stmt = $conn->prepare("DELETE FROM diarios WHERE dateuser=:a");
			$stmt->bindParam(':a', $dateuserPar);
			$stmt->execute();
			$conn = null;
			echo $dateuserPar . "eliminado";
			exit();
		} else {
			throw new Exception("No se encontró el registro en la base de datos");
		}
	} catch(Exception $e) {
	    echo 'Error: ' . $e->getMessage();
	    exit();
	}

	function yaCargado($dateuser) {
		$cargado = true;

		$conn = null;
		$conn = new PDO("pgsql:host=localhost;dbname=diariodb", "postgres", "12345");
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		$stmt = $conn->prepare("SELECT * FROM diarios WHERE dateuser=:a");
		$stmt->bindParam(':a', $dateuser);

		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if (empty($result))
			$cargado = false;

		$conn = null;

		return $cargado;
	}

	function tengoPermiso($dateuser) {
		$permiso = false;

		$conn = null;
		$conn = new PDO("pgsql:host=localhost;dbname=diariodb", "postgres", "12345");
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		$stmt = $conn->prepare("SELECT * FROM diarios WHERE dateuser=:a");
		$stmt->bindParam(':a', $dateuser);

		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if (empty($result))
			throw new Exception("Desde tengoPermiso(), No se encontró el registro en la base de datos");

		if ($result[0]['owner'] == $_SESSION['diario_user_logged'])
			$permiso = true;
	
		$conn = null;

		return $permiso;
	}
?>