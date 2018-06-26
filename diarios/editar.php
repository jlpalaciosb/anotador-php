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
			
			var searchInput = $('#ta');
			// Multiply by 2 to ensure the cursor always ends up at the end;
			// Opera sometimes sees a carriage return as 2 characters.
			var strLength = searchInput.val().length * 2;
			searchInput.focus();
			searchInput[0].setSelectionRange(strLength, strLength);
		});

        // Enable navigation prompt
		window.onbeforeunload = function() {
	    	return true;
		};
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
				<textarea id="ta" rows="4" name="content" class="form-control"><?php echo $content ?></textarea>
			</div>
			<button type="submit" class="btn btn-primary" onclick="window.onbeforeunload=null">Guardar</button>
		</form>

		<!--Footer-->
		<?php include $_SERVER['DOCUMENT_ROOT'] . '/include/footer.php'; ?>
	</div>
</body>
</html>

<?php
	function hacerEnGet() {
		if (isset($_GET['date']) && !empty($_GET['date']) && !format_error_YMD($_GET['date'])) {
			$GLOBALS['date_user'] = $_GET['date'] . '-' . $_SESSION['logged_user'];

			$conn = new PDO('pgsql:host=localhost;dbname=diariodb', 'postgres', '12345');
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			$stmt = $conn->prepare('SELECT * FROM diarios WHERE dateuser=:a');
			$stmt->bindParam(':a', $GLOBALS['date_user']);

			$stmt->execute();
			$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if (empty($result)) {
				http_response_code(404);
				include($_SERVER['DOCUMENT_ROOT'] . '/include/404.php');		
				exit();
			}

			$GLOBALS['content'] = openssl_decrypt($result[0]['content'], 'AES-128-CBC', $_SESSION['user_md5'], 0, '0000000000000000');

			$conn = null;
		} else {
			http_response_code(400);
			include($_SERVER['DOCUMENT_ROOT'] . '/include/400.php');		
			exit();
		}
	}

	function hacerEnPost() {
		if(isset($_POST['content']) && isset($_POST['date']) && !empty($_POST['date']) && !format_error_YMD($_POST['date'])) {
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
			http_response_code(400);
			include($_SERVER['DOCUMENT_ROOT'] . '/include/400.php');		
			//exit();
		}
		exit();
	}
?>
