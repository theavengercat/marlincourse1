<?php
session_start();
if(!isset($_SESSION['hash'])) {
	header("Location: login.php");
	die();
}

var_dump($_SESSION);
