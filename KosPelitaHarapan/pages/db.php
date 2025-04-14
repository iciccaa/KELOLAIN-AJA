<?php 
$host = 'localhost';
$user = 'root' ;
$pass = "";
$db = 'kos_pelita';

$kon = mysqli_connect($host, $user, $pass, $db);
if (!$kon) {
    die("Connection failed: " . mysqli_connect_error());
}
