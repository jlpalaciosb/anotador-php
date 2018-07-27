<?php
	require $_SERVER['DOCUMENT_ROOT'] . '/include/utilidades.php';

	session_start();

	if (isset($_SESSION['logged_user'])) { /*Ya inició sesión*/
		header('Location: /index.php');
		exit();
	}

	$username = $password = $error = '';
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		if (!empty($_POST['username']) && !empty($_POST['password'])) {
			if (authenticate($_POST['username'], $_POST['password'])) {
				$_SESSION['logged_user'] = $_POST['username'];
				$_SESSION['user_md5'] = md5($_POST['password'] . 'xxx'); //clave para encriptar y desencriptar los diarios
				header('Location: /index.php');
				exit();
			} else {
				$error = 'Nombre de usuario o contraseña incorrectos';
			}
		}
		if (!empty($_POST['username'])) {
			$username = $_POST['username'];
		}
		if (!empty($_POST['password'])) {
			$password = $_POST['password'];
		}
	}
?>

<!DOCTYPE html>
<html lang="es">
<head>
	<title>Diario App Login</title>
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
			<form class="cuadro" method="post" action="/login/index.php">
				<h2>Iniciar Sesión</h2>
				<div class="form-group">
					<label for="username">Nombre de Usuario</label>
					<input autofocus required class="form-control" type="text" name="username" placeholder="Ingresa tu nombre de usuario" value="<?php echo $username;?>">
				</div>
				<div class="form-group">
					<label for="password">Contraseña</label>
					<input required class="form-control" type="password" name="password" placeholder="Ingresa tu contraseña" value="<?php echo $password;?>">
				</div>
				<p class="error"><?php echo $error; ?></p>
				<center><button type="submit" class="btn btn-primary">Iniciar Sesión</button></center>
				<p style="text-align: right;margin-top: 15px;margin-bottom: 0px;"><a href="/login/register.php">Crear Cuenta</a></p>
			</form>

			<div style="margin-top: 10px;"></div>
			<!--Footer-->
			<?php include $_SERVER['DOCUMENT_ROOT'] . '/include/footer.php'; ?>
		</div>
	</div>
</body>
</html>
