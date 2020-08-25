<?php
	require_once(realpath($_SERVER['DOCUMENT_ROOT'] . '/..') . '/resources/config.php');
	require_once(LIBRARY_PATH . '/database.php');
	require_once(LIBRARY_PATH . '/utilities.php');

	session_start();
	if (!isset($_SESSION['user'])) {
		header('Location: /login/index.php');
		exit();
	}

	$content = '';
	
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
	<link rel="shortcut icon" type="image/png" href="/assets/img/diarioapp.png"/>
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<link rel="stylesheet" type="text/css" href="/assets/bootstrap-3.3.7/dist/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="/assets/css/global.css">
	<link rel="stylesheet" type="text/css" href="style.css">

	<script type="text/javascript" src="/assets/jquery-3.3.1.min.js"></script>
	<script type="text/javascript" src="/assets/bootstrap-3.3.7/dist/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="/assets/autosize.min.js"></script>
	
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
		<?php include TEMPLATES_PATH . '/encabezado.php' ?>

		<h1 class="outside">
			<?php echo legible_date($_GET['date']) ?>
		</h1>
		<form class="cuadro" method="post" action="/entry/edit.php">
			<input style="display:none;" type="text" name="date" value="<?php echo $_GET["date"]; ?>">
			<div class="form-group">
				<label for="content">Escribe tus notas</label>
				<textarea id="ta" rows="4" name="content" class="form-control"><?php echo $content ?></textarea>
			</div>
			<button type="submit" class="btn btn-primary" onclick="window.onbeforeunload=null">Guardar</button>
		</form>

		<!--Footer-->
		<?php include TEMPLATES_PATH . '/footer.php' ?>
	</div>
</body>
</html>

<?php
	function hacerEnGet() {
		if (isset($_GET['date']) && !empty($_GET['date']) && !format_error_YMD($_GET['date'])) {
			$stmt = $GLOBALS['connection']->prepare('SELECT * FROM entries WHERE date=:d AND owner=:o');
			$stmt->bindParam(':d', $_GET['date']);
			$stmt->bindParam(':o', $_SESSION['user']);
			$stmt->execute();
			$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if (empty($result)) {
				http_response_code(404);
				include(TEMPLATES_PATH . '/404.php');		
				exit();
			}
			$GLOBALS['content'] = decrypt($result[0]['content'], $_SESSION['crypt_key']);
		} else {
			http_response_code(400);
			include(TEMPLATES_PATH . '/400.php');		
			exit();
		}
	}

	function hacerEnPost() {
		if(isset($_POST['content']) && isset($_POST['date']) && !empty($_POST['date']) && !format_error_YMD($_POST['date'])) {
			$stmt = $GLOBALS['connection']->prepare('UPDATE entries SET content=:c WHERE date=:d AND owner=:o');
			$stmt->bindParam(':c', $_POST['content']);
			$stmt->bindParam(':d', $_POST['date']);
			$stmt->bindParam(':o', $_SESSION['user']);
			$_POST['content'] = encrypt($_POST['content'], $_SESSION['crypt_key']);
			$stmt->execute();
			header('Location: /entry/view.php?date=' . $_POST['date']);
			exit();
		} else {
			http_response_code(400);
			include(TEMPLATES_PATH . '/400.php');		
			exit();
		}
	}
?>
