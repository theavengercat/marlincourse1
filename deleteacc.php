<?php
ini_set('display_errors','Off');
session_start();
include('app/config.php');
include('app/user.func.php');


if(isset($_SESSION['hash']) and ($_SESSION['id'] == $_GET['id'] or isAdmin()))
	deleteAccount($_GET['id']);


?>