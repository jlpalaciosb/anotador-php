<?php
	require $_SERVER['DOCUMENT_ROOT'] . '/include/utilidades.php';

	session_start();
	if (!isset($_SESSION['logged_user'])) {
		header('Location: /login/index.php');
		exit();
	}

	$date_user = $content = '';
	
	if ($_SERVER['REQUEST_METHOD'] == 'GET') {
		hacerEnGet();
	} else {
		hacerEnPost();
	}
?>

<!DOCTYPE html>
<html lang="es">
<head>
	<title>Editar</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="/bootstrap-3.3.7/dist/css/bootstrap.css">
	<link rel="stylesheet" href="/style.css">
	<link rel="shortcut icon" type="image/png" href="/res/diarioapp.png"/>
	<meta charset="UTF-8">
	<script>
        var textarea = null;
        window.addEventListener("load", function() {
            textarea = window.document.querySelector("textarea");
            textarea.addEventListener("keypress", function() {
                if(textarea.scrollTop != 0){
                    textarea.style.height = textarea.scrollHeight + 2 +"px";
                }
            }, false);
            textarea.style.height = textarea.scrollHeight + 2 +"px";
        }, false);
    </script>
</head>
<body>
	<div class="container">
		<!--Encabezado?-->
		<?php include $_SERVER["DOCUMENT_ROOT"] . "/include/encabezado.php"; ?>

		<h1 class="outside">
			<?php echo legible_dateuser($date_user) ?>
		</h1>
		<form class="cuadro" method="post" action="/diarios/editar.php">
			<input style="display:none;" type="text" name="date" value="<?php echo $_GET["date"]; ?>">
			<div class="form-group">
				<label for="content">Edite este diario</label>
				<textarea name="content" class="form-control"><?php echo $content ?></textarea>
			</div>
			<button type="submit" class="btn btn-primary">Guardar</button>
		</form>
	</div>
</body>
</html>

<?php
	function hacerEnGet() {
		if (isset($_GET['date']) && !empty($_GET['date'])) {
			$GLOBALS['date_user'] = $_GET['date'] . '-' . $_SESSION['logged_user'];

			$conn = new PDO('pgsql:host=localhost;dbname=diariodb', 'postgres', '12345');
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			$stmt = $conn->prepare('SELECT * FROM diarios WHERE dateuser=:a');
			$stmt->bindParam(':a', $GLOBALS['date_user']);

			$stmt->execute();
			$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if (empty($result)) {
				echo 'No se encontró registro en la base de datos';
				exit();
			}

			$GLOBALS['content'] = openssl_decrypt($result[0]['content'], 'AES-128-CBC', $_SESSION['user_md5'], 0, '0000000000000000');

			$conn = null;
		} else {
			echo 'Error de parametro get';
			exit();
		}
	}

	function hacerEnPost() {
		if(isset($_POST['content']) && isset($_POST['date']) && !empty($_POST['date'])) {
			$_POST['content'] = openssl_encrypt($_POST['content'], 'AES-128-CBC', $_SESSION['user_md5'], 0, '0000000000000000');
			$GLOBALS['date_user'] = $_POST['date'] . '-' . $_SESSION['logged_user'];
			
			$conn = new PDO('pgsql:host=localhost;dbname=diariodb', 'postgres', '12345');
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			$stmt = $conn->prepare('UPDATE diarios SET content=:a WHERE dateuser=:b');
			$stmt->bindParam(':a', $_POST['content']);
			$stmt->bindParam(':b', $GLOBALS['date_user']);
			$stmt->execute();

			$conn = null;

			header('Location: /diarios/ver.php?date=' . $_POST['date']);
		} else {
			echo 'Error de datos post';
		}
		exit();
	}
?>
