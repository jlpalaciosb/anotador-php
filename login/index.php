<?php
	session_start();

	if (isset($_SESSION['diario_user_logged'])) { /*Ya inició sesión*/
		header("Location: /index.php");
		exit();
	}

	$username = $password = $error = '';
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		if (!empty($_POST['username']) && !empty($_POST['password'])) {
			if (autenticar($_POST['username'], $_POST['password'])) {
				$_SESSION['diario_user_logged'] = $_POST['username'];
				header("Location: /index.php");
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
			<form method="post" action="/login/index.php">
				<h1>Iniciar Sesión</h1>
				<div class="form-group">
					<label for="username">Nombre de Usuario</label>
					<input required class="form-control" type="text" name="username" placeholder="Ingrese su nombre de usuario" value="<?php echo $username;?>">
				</div>
				<div class="form-group">
					<label for="password">Contraseña</label>
					<input required class="form-control" type="password" name="password" placeholder="Ingrese su contraseña" value="<?php echo $password;?>">
				</div>
				<p class="error"><?php echo $error; ?></p>
				<center><button type="submit" class="btn btn-primary">Iniciar Sesión</button></center>
				<p style="text-align: right;margin-top: 15px;margin-bottom: 0px;"><a href="/login/register.php">Crear Cuenta</a></p>
			</form>
		</div>
	</div>
</body>
</html>

<?php
	function autenticar($username, $password) {
		$correcto = false;
		$conn = null;
	    try {
    		$conn = new PDO("pgsql:host=localhost;dbname=diariodb", "postgres", "12345");
    		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    		
    		$stmt = $conn->prepare("SELECT * FROM users WHERE username=:a AND password_md5=:b");
    		$stmt->bindParam(':a', $username);
    		$stmt->bindParam(':b', $password);

    		$password = md5($password);
    		$stmt->execute();

    		if ($stmt->rowCount() == 1) {
    			$correcto = true;
    		}

		} catch(PDOException $e) {
		    echo "Error: " . $e->getMessage();
		}
		$conn = null;
		return $correcto;
	}
?>