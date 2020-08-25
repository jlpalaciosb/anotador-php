<?php
	require_once(realpath($_SERVER['DOCUMENT_ROOT'] . '/..') . '/resources/config.php');
	require_once(LIBRARY_PATH . '/database.php');

	session_start();

	if(isset($_SESSION['user'])) { # Already started session
		header('Location: /index.php');
		exit();
	}

	$error = $user = '';
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		$user = $_POST['user'];
		if (registered($_POST['user'])) {
			$error = 'El nombre ya está en uso';
		} else if (strlen($_POST['user']) < 4) {
			$error = 'Nombre muy corto';
		} else if (strlen($_POST['user']) > 10) {
			$error = 'Nombre muy largo';
		} else if (preg_match('/[^A-Za-z0-9]/', $_POST['user'])) {
			$error = 'Nombre no válido';
		} else if (strlen($_POST['password']) < 8) {
			$error = 'La contraseña debe tener como mínimo 8 caracteres';
		} else if ($_POST['password'] != $_POST['password_confirm']) {
			$error = 'Las contraseñas no coinciden';
		}

		if ($error == '') {
			register($_POST['user'], $_POST['password']);
			$_SESSION['user'] = $_POST['user'];
			$_SESSION['crypt_key'] = md5($_POST['user'] .$_POST['password']); # key for encrypting and decrypting diarys
			header('Location: /index.php');
			exit();
		}
	}
?>

<!DOCTYPE html>
<html lang="es">
<head>
	<title>Diario App Register</title>
	<meta charset="UTF-8">
	<link rel="shortcut icon" type="image/png" href="/assets/img/diarioapp.png"/>
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<link rel="stylesheet" type="text/css" href="/assets/bootstrap-3.3.7/dist/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="/assets/css/global.css">
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
	<div class="container">
		<div class="form-container">
			<img src="/assets/img/diarioapp2.png" style="margin-bottom: 5px">
			<form class="cuadro" method="post" action="/user/register.php">
				<h2>Crea una cuenta</h2>
				<div class="form-group">
					<label for="new_user">Nombre de Usuario</label>
					<input required class="form-control" type="text" name="user" placeholder="Elige un nombre de usuario" value="<?php echo $user ?>" autofocus>
				</div>
				<div class="form-group">
					<label for="pass">Contraseña</label>
					<input required class="form-control" type="password" name="password" placeholder="Ingresa una contraseña">
				</div>
				<div class="form-group">
					<label for="pass_conf">Confirmar</label>
					<input required class="form-control" type="password" name="password_confirm" placeholder="Confirma tu contraseña">
				</div>
				<p class="error"><?php echo $error; ?></p>
				<center><button type="submit" class="btn btn-primary">Crear Cuenta</button></center>
				<p style="text-align: right;margin-top: 15px;margin-bottom: 0px;"><a href="/user/login.php">Iniciar Sesión</a></p>
			</form>

			<div style="margin-top: 10px;"></div>
			<!--Footer-->
			<?php include(TEMPLATES_PATH . '/footer.php'); ?>
		</div>
	</div>
</body>
</html>
