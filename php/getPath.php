<?php

include_once('connexion.php');

if (!check_post('hash'))
{
	echo "error";
	exit;
}

$req = $bdd->prepare('SELECT path, downloaded FROM hash WHERE hash = ?');
$req->execute(array($_POST['hash']));

if ($req->rowCount() != 1)
{
	echo "error";
	exit;
}

$data = $req->fetch();

if (substr($data['path'], -4) == ".avi")
{
	echo "error";
	exit;
}

if ($data['downloaded'] < 1)
{
	echo "error";
	exit;
}

$arr = [];

$arr[0] = explode('/', $data['path'])[0];

$arr[1] = 0;
if (file_exists("../films/".$arr[0]."/en.vtt"))
	$arr[1] = 1;

$arr[2] = 0;
if (file_exists("../films/".$arr[0]."/fr.vtt"))
	$arr[2] = 1;

echo json_encode($arr);

?>
