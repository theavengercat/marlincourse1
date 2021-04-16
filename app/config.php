<?php

$dbname = 'project';
$host = 'localhost';
$login = 'root';
$password = 'root';
$dbh = new PDO('mysql:dbname='.$dbname.';host='.$host, $login, $password);

?>