<?php
	require_once(realpath($_SERVER['DOCUMENT_ROOT'] . '/..') . '/resources/config.php');
?>

<!DOCTYPE html>
<html lang="es">
<head>
	<title>Not found</title>
	<meta charset="UTF-8">
	<link rel="shortcut icon" type="image/png" href="/assets/img/diarioapp.png"/>
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<link rel="stylesheet" type="text/css" href="/assets/bootstrap-3.3.7/dist/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="/assets/css/global.css">
</head>
<body>
	<div class="container">
		<?php include TEMPLATES_PATH . '/encabezado.php' ?>
		<div class="cuadro">
			<p class="error margintop">Error!</p>
			<p>Not found</p>
		</div>
		<?php include TEMPLATES_PATH . '/footer.php' ?>
	</div>
</body>
</html>
