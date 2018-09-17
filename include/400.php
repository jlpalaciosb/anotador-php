<!DOCTYPE html>
<html lang="es">
<head>
	<title>Bad Request</title>
	<meta charset="UTF-8">
	<link rel="shortcut icon" type="image/png" href="/res/diarioapp.png"/>
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<link rel="stylesheet" type="text/css" href="/res/bootstrap-3.3.7/dist/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="/res/global.css">
	<link rel="stylesheet" type="text/css" href="style.css">

	<script type="text/javascript" src="/res/jquery-3.3.1.min.js"></script>
	<script type="text/javascript" src="/res/bootstrap-3.3.7/dist/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="/res/autosize.min.js"></script>
</head>
<body>
	<div class="container">
		<?php include $_SERVER['DOCUMENT_ROOT'] . '/include/encabezado.php' ?>
		<div class="cuadro">
			<p class="error margintop">Error!</p>
			<p>Bad Request</p>
		</div>
		<?php include $_SERVER['DOCUMENT_ROOT'] . '/include/footer.php' ?>
	</div>
</body>
</html>