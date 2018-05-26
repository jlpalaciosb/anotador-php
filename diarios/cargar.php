<?php
	session_start();

	if (!isset($_SESSION['diario_user_logged'])) {
		header("Location: /login/index.php");
		exit();
	}

	$dateuserPar = "";
	try {
		if (!isset($_GET["dateuser"]) || empty($_GET["dateuser"]) || format_error($_GET["dateuser"]))
			throw new Exception("Error de parametro get");

		$dateuserPar = $_GET["dateuser"];

		if (yaCargado($dateuserPar)) {
			if(!tengoPermiso($dateuserPar)) {
				throw new Exception("No tienes permiso");
			}
		} else {
			$conn = null;
			$conn = new PDO("pgsql:host=localhost;dbname=diariodb", "postgres", "12345");
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			$stmt = $conn->prepare("INSERT INTO diarios (dateuser,owner,content) VALUES (:a,:b,'')");
			$stmt->bindParam(':a', $dateuserPar);
			$stmt->bindParam(':b', $_SESSION['diario_user_logged']);

			$stmt->execute();
			$conn = null;
		}
		header("Location: /diarios/editar.php?dateuser=" . $dateuserPar);
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

	#El formato del $dateuser deber ser YYYY-MM-DD-user
	#Tambien sirve para mitigar ataques de malintencionados
	function format_error ($dateuser) {
		$correct_len = 11 + strlen($_SESSION["diario_user_logged"]);
		if (strlen($dateuser) != $correct_len) {
			return true;
		}
		if (substr($dateuser, 11) != $_SESSION["diario_user_logged"]) {
			return true;
		}
		return false;
	}

	function charAt($str,$pos) {
    	return (substr($str,$pos,1) !== false) ? substr($str,$pos,1) : -1;
	}
?>