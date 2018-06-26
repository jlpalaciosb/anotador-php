<?php
	echo '<div style="margin-top: 10px;">';
	echo '<a href="/">';
	echo '<img id="logo" src="/res/diarioapp2.png">' . "\n";
	echo '</a>';
	echo '<div id="account" class="cuadro">' . "\n";
	echo '<span>( ' . $_SESSION['logged_user'] . ' )</span>' . "\n";
	echo '<a href="/login/logout.php">Salir</a>' . "\n";
	echo '</div>' . "\n";
	echo '<div class="clearman"></div>' . "\n";
	echo '</div>';
?>
