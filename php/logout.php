<?php 
session_start();
$_SESSION['id'] = "";
$_SESSION['login'] = "";

header('Location: /');
?>