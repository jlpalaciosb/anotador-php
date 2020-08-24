<?php
	require_once(realpath($_SERVER['DOCUMENT_ROOT'] . '/..') . '/resources/config.php');
	require_once(LIBRARY_PATH . '/database.php');

	session_start();

	if(isset($_SESSION['user'])) { #Already started session
		header('Location: /index.php');
		exit();
	}

	$user = $error = '';
	if($_SERVER['REQUEST_METHOD'] == 'POST') {
		if (authenticate($_POST['user'], $_POST['password'])) {
			$_SESSION['user'] = $_POST['user'];
			$_SESSION['crypt_key'] = md5($_POST['user'] . $_POST['password']); # key for encrypting and decrypting diarys
			header('Location: /index.php');
			exit();
		} else {
			$error = 'Nombre de usuario o contraseña incorrectos';
			$user = $_POST['user'];
		}
	}
?>

<!DOCTYPE html>
<html lang="es">
<head>
	<title>Diario App Login</title>
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
			<img src="/assets/img/diarioapp2.png">
			<form class="cuadro" method="post" action="/user/login.php">
				<h2>Iniciar Sesión</h2>
				<div class="form-group">
					<label for="username">Nombre de Usuario</label>
					<input autofocus required class="form-control" type="text" name="user" placeholder="Ingresa tu nombre de usuario" value="<?php echo $user;?>">
				</div>
				<div class="form-group">
					<label for="password">Contraseña</label>
					<input required class="form-control" type="password" name="password" placeholder="Ingresa tu contraseña">
				</div>
				<p class="error"><?php echo $error; ?></p>
				<center><button type="submit" class="btn btn-primary">Iniciar Sesión</button></center>
				<p style="text-align: right;margin-top: 15px;margin-bottom: 0px;"><a href="/user/register.php">Crear Cuenta</a></p>
			</form>

			<div style="margin-top: 10px;"></div>
			<!--Footer-->
			<?php include(TEMPLATES_PATH . '/footer.php'); ?>
		</div>
	</div>
</body>
</html>
