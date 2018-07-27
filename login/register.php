<?php
	require $_SERVER['DOCUMENT_ROOT'] . '/include/utilidades.php';

	session_start();

	if (isset($_SESSION['logged_user'])) {
		header('Location: /index.php');
		exit();
	}

	$new_user = $pass = $pass_conf = $error = '';
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		if (!empty($_POST['new_user']) && !empty($_POST['pass'] && !empty($_POST['pass_conf']))) {
			$new_user = $_POST['new_user'];
			$pass = $_POST['pass'];
			$pass_conf = $_POST['pass_conf'];
			
			if (registered($new_user)) {
				$error = 'El nombre ya está en uso';
			} else if (strlen($new_user) < 4) {
				$error = 'Elija un nombre de no menos de 4 letras';
			} else if (strlen($new_user) > 10) {
				$error = 'Elija un nombre de no más de 10 letras';
			} else if (preg_match('/[^A-Za-z0-9]/', $new_user)) {
				$error = 'Nombre incorrecto';
			} else if (strlen($pass) < 8) {
				$error = 'La contraseña debe tener como mínimo 8 caracteres';
			} else if (strlen($pass) > 16) {
				$error = 'La contraseña puede tener como máximo 16 caracteres';
			} else if ($pass != $pass_conf) {
				$error = 'Las contraseñas no coinciden';
			}

			if ($error == '') {
				register($new_user, $pass);
				$_SESSION['logged_user'] = $new_user;
				$_SESSION['user_md5'] = md5($pass . 'xxx'); //clave para encriptar y desencriptar los diarios
				header('Location: /index.php');
				exit();
			}
		}
	}
?>

<!DOCTYPE html>
<html lang="es">
<head>
	<title>Diario App Register</title>
	<meta charset="UTF-8">
	<link rel="shortcut icon" type="image/png" href="/res/diarioapp.png"/>
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<link rel="stylesheet" type="text/css" href="/res/bootstrap-3.3.7/dist/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="/res/global.css">
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
	<div class="container">
		<div class="form-container">
			<img src="/res/diarioapp2.png">
			<form class="cuadro" method="post" action="/login/register.php">
				<h2>Crear cuenta</h2>
				<div class="form-group">
					<label for="new_user">Nombre de Usuario</label>
					<input required class="form-control" type="text" name="new_user" placeholder="Escoge un nombre de usuario" value="<?php echo $new_user ?>" autofocus>
				</div>
				<div class="form-group">
					<label for="pass">Contraseña</label>
					<input required class="form-control" type="password" name="pass" placeholder="Ingresa una contraseña" value="<?php echo $pass ?>">
				</div>
				<div class="form-group">
					<label for="pass_conf">Confirmar</label>
					<input required class="form-control" type="password" name="pass_conf" placeholder="Confirma tu contraseña" value="<?php echo $pass_conf ?>">
				</div>
				<p class="error"><?php echo $error; ?></p>
				<center><button type="submit" class="btn btn-primary">Crear Cuenta</button></center>
				<p style="text-align: right;margin-top: 15px;margin-bottom: 0px;"><a href="/login/index.php">Iniciar Sesión</a></p>
			</form>

			<div style="margin-top: 10px;"></div>
			<!--Footer-->
			<?php include $_SERVER['DOCUMENT_ROOT'] . '/include/footer.php'; ?>
		</div>
	</div>
</body>
</html>
