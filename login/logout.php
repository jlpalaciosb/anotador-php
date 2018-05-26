<<?php
	session_start();
	session_unset();
	session_destroy();
	header("Location: /diario/login/index.php");
?>