<?php
	session_start();
	if (!isset($_SESSION['diario_user_logged'])) {
		header("Location: /login/index.php");
		exit();
	}

	$dateuserPar = $dateuserDb = $ownerDb = $contentDb = "";
	try {
		if (isset($_GET["dateuser"]) && !empty($_GET["dateuser"])) {
			$dateuserPar = $_GET["dateuser"];

			$conn = null;
			$conn = new PDO("pgsql:host=localhost;dbname=diariodb", "postgres", "12345");
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			$stmt = $conn->prepare("SELECT * FROM diarios WHERE dateuser=:a");
			$stmt->bindParam(':a', $dateuserPar);

			$stmt->execute();
			$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if (empty($result)) {
				throw new Exception("No se encontró registro en la base de datos");
			}

			$dateuserDb = $result[0]['dateuser'];
			$ownerDb = $result[0]['owner'];
			$contentDb = $result[0]['content'];

			if ($ownerDb != $_SESSION['diario_user_logged']) {
				throw new Exception("No tienes permiso");
			}
		
			$conn = null;
		} else {
			throw new Exception("Error de parametro get");
		}
	} catch(Exception $e) {
	    echo '<p class="error">Error: ' . $e->getMessage() . "</p>";
	    exit();
	}
?>

<!DOCTYPE html>
<html lang="es">
<head>
	<title>Ver</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="/bootstrap-3.3.7/dist/css/bootstrap.css">
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
		<h1><?php echo $dateuserDb ?></h1>
		<textarea readonly class="form-control"><?php echo $contentDb ?></textarea>
		<a href="<?php echo '/diarios/editar.php?dateuser=' . $_GET['dateuser']; ?>">Editar</a>
		<br>
		<a href="/">Página Principal</a>
	</div>
</body>
</html>