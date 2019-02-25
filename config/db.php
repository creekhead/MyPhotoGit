<?php
$servername = "localhost";
$username = "pk";
$password = "";
$dbname = "ourpictures";

// Create connection
$conn = new mysqli($servername, $username, $password,$dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}else{
	//echo "Mysql Connected".PHP_EOL;
}

include_once('functions.php');

?>