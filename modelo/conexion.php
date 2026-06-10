<?php
    $host="localhost";
    $user="root";
    $password="";
    $db="isunki_db";

    $conn = mysqli_connect($host,$user,$password,$db);
	
	if(!$conn){
		die("Error de conexión: " . mysqli_connect_error());
	}

    mysqli_set_charset($conn, "utf8mb4");
?>