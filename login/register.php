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
			if (!registered($new_user)) {
				if ($pass != $pass_conf) {
					$error = 'Las contraseñas no coinciden';
				} else if (strlen($new_user) > 10) {
					$error = 'Elija un nombre más corto';
				} else if (register($new_user, $pass)) {
					$_SESSION['logged_user'] = $new_user;
					$_SESSION['user_md5'] = md5($pass . 'xxx'); //clave para encriptar y desencriptar los diarios
					header('Location: /index.php');
					exit();
				}
			} else {
				$error = 'Elija otro nombre';
			}
		}
	}
?>

<!DOCTYPE html>
<html lang="es">
<head>
	<title>Diario App Register</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="/bootstrap-3.3.7/dist/css/bootstrap.css">
	<link rel="stylesheet" href="style.css">
	<link rel="shortcut icon" type="image/png" href="/res/diarioapp.png"/>
	<meta charset="UTF-8">
</head>
<body>
	<div class="container">
		<div class="form-container">
			<img src="/res/diarioapp2.png">
			<form method="post" action="/login/register.php">
				<h1>Crear cuenta</h1>
				<div class="form-group">
					<label for="new_user">Nombre de Usuario</label>
					<input required class="form-control" type="text" name="new_user" placeholder="Ingrese su nombre de usuario" value="<?php echo $new_user ?>">
				</div>
				<div class="form-group">
					<label for="pass">Contraseña</label>
					<input required class="form-control" type="password" name="pass" placeholder="Ingrese una contraseña" value="<?php echo $pass ?>">
				</div>
				<div class="form-group">
					<label for="pass_conf">Confirmar</label>
					<input required class="form-control" type="password" name="pass_conf" placeholder="Confirme su contraseña" value="<?php echo $pass_conf ?>">
				</div>
				<p class="error"><?php echo $error; ?></p>
				<center><button type="submit" class="btn btn-primary">Crear Cuenta</button></center>
				<p style="text-align: right;margin-top: 15px;margin-bottom: 0px;"><a href="/login/index.php">Iniciar Sesión</a></p>
			</form>
		</div>
	</div>
</body>
</html>
