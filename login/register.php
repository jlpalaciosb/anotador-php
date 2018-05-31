<?php
	session_start();

	if (isset($_SESSION['diario_user_logged'])) { /*Ya inició sesión*/
		header("Location: /index.php");
		exit();
	}

	$newUser = $pass = $passC = $error = '';
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		if (!empty($_POST['newUser']) && !empty($_POST['pass'] && !empty($_POST['passC']))) {
			$newUser = $_POST['newUser'];
			$pass = $_POST['pass'];
			$passC = $_POST['passC'];
			if (!yaRegistrado($newUser)) {
				if ($pass != $passC) {
					$error = "Las contraseñas no coinciden";
				} else if (strlen($newUser) > 10) {
					$error = "Elija un nombre más corto";
				} else if (registrar($newUser, $pass)) {
					$_SESSION['diario_user_logged'] = $newUser;
					$_SESSION['user_password_md5'] = md5($_POST['pass'] . "xxx"); //clave para encriptar y desencriptar los diarios
					header("Location: /index.php");
					exit();
				}
			} else {
				$error = "Elija otro nombre";
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
					<label for="newUser">Nombre de Usuario</label>
					<input required class="form-control" type="text" name="newUser" placeholder="Ingrese su nombre de usuario" value="<?php echo $newUser;?>">
				</div>
				<div class="form-group">
					<label for="pass">Contraseña</label>
					<input required class="form-control" type="password" name="pass" placeholder="Ingrese una contraseña" value="<?php echo $pass;?>">
				</div>
				<div class="form-group">
					<label for="passC">Confirmar</label>
					<input required class="form-control" type="password" name="passC" placeholder="Confirme su contraseña" value="<?php echo $passC;?>">
				</div>
				<p class="error"><?php echo $error; ?></p>
				<center><button type="submit" class="btn btn-primary">Crear Cuenta</button></center>
				<p style="text-align: right;margin-top: 15px;margin-bottom: 0px;"><a href="/login">Iniciar Sesión</a></p>
			</form>
		</div>
	</div>
</body>
</html>

<?php
	function yaRegistrado($username) {
		$registrado = true;
		$conn = null;
	    try {
    		$conn = new PDO("pgsql:host=localhost;dbname=diariodb", "postgres", "12345");
    		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    		
    		$stmt = $conn->prepare("SELECT * FROM users WHERE username=:a");
    		$stmt->bindParam(':a', $username);

    		$stmt->execute();

    		if ($stmt->rowCount() == 0) {
    			$registrado = false;
    		}

		} catch(PDOException $e) {
		    echo '<p class="error">Error: ' . $e->getMessage() . "</p>";
		}
		$conn = null;
		return $registrado;
	}

	function registrar($username, $password) {
		$conn = null;
	    try {
    		$conn = new PDO("pgsql:host=localhost;dbname=diariodb", "postgres", "12345");
    		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    		
    		$stmt = $conn->prepare("INSERT INTO users (username, password_md5) VALUES (:a, :b)");
    		$stmt->bindParam(':a', $username);
    		$stmt->bindParam(':b', $password);

    		$password = md5($password);

    		$stmt->execute();
		} catch(PDOException $e) {
		    echo '<p class="error">Error: ' . $e->getMessage() . "</p>";
		    return false;
		}
		$conn = null;
		return true;
	}
?>