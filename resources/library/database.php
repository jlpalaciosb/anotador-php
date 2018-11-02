<?php

# Database connection
$dbhost = $config['db']['host'];
$dbname = $config['db']['name'];
$dbuser = $config['db']['user'];
$dbpass = $config['db']['pass'];
$connection = new PDO("pgsql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);

# Return true if exists a diary entry of the user $user with date $date
function exists_entry($user, $date) {
	$stmt = $GLOBALS['connection']->prepare("SELECT * FROM entries WHERE owner=:o AND date=:d");
	$stmt->bindParam(':o', $user);
	$stmt->bindParam(':d', $date);
	$stmt->execute();
	return $stmt->rowCount() == 1;
}

# Return true if the $user is in the database
function registered($user) {
	$stmt = $GLOBALS['connection']->prepare('SELECT * FROM users WHERE name=:u');
	$stmt->bindParam(':u', $user);
	$stmt->execute();
	return $stmt->rowCount() == 1;
}

# Register a user
function register($user, $password) {
	$stmt = $GLOBALS['connection']->prepare('INSERT INTO users (name, password) VALUES (:n, :p)');
	$stmt->bindParam(':n', $user);
	$stmt->bindParam(':p', $password);
	$password = md5($password);
	$stmt->execute();
}

# Return true if the $user and $password are correct
function authenticate($user, $password) {
	$stmt = $GLOBALS['connection']->prepare('SELECT * FROM users WHERE name=:n AND password=:p');
	$stmt->bindParam(':n', $user);
	$stmt->bindParam(':p', $password);
	$password = md5($password);
	$stmt->execute();
	return $stmt->rowCount() == 1;
}
