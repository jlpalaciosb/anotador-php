<?php
	echo "<a href=\"/\">";
	echo "<img id=\"logo\" src=\"/res/diarioapp2.png\">\n";
	echo "</a>";
	echo "<div id=\"account\" class=\"cuadro\">\n";
	echo "<span>( " . $_SESSION["diario_user_logged"] . " )</span>\n";
	echo "<a href=\"/login/logout.php\"> Salir</a>\n";
	echo "</div>\n";
	echo "<div class=\"clearman\"></div>\n";
?>