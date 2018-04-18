<?php

date_default_timezone_set("Europe/Paris");
try
{
	$bdd = new PDO('mysql:dbname=hypertube;host=127.0.0.1;charset=utf8', 'root', 'root');
	$bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$bdd->exec("SET NAMES 'UTF8'");
}
catch (Exception $e)
{
	header('Location: /error.php');
	exit;
}

$req = $bdd->prepare('SELECT path, id_hash FROM hash WHERE date <= ? - ?');
$req->execute(array(time(), 2629746));

while ($data = $req->fetch())
{
	$path = substr($data['path'], 0, strpos($data['path'], "/"));
	if ($path == "")
		continue ;
	delete_folder($_SERVER['DOCUMENT_ROOT']."/films/".$path);
	$reqb = $bdd->prepare('DELETE FROM hash WHERE id_hash = ?');
	$reqb->execute(array($data['id_hash']));
}

session_start();

if (!isset($_SESSION['id']))
	$_SESSION['id'] = "";
if (!isset($_SESSION['login']))
	$_SESSION['login'] = "";
if(!isset($_SESSION["lang"]) || $_SESSION['lang'] == "")
{
	$_SESSION['lang'] = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
	if(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) == "fr")
		include_once($_SERVER['DOCUMENT_ROOT']."/language/french.php");
	else
		include_once($_SERVER['DOCUMENT_ROOT']."/language/english.php");
}
else if($_SESSION["lang"] == "fr")
	include_once($_SERVER['DOCUMENT_ROOT']."/language/french.php");
else
	include_once($_SERVER['DOCUMENT_ROOT']."/language/english.php");

if ($_SESSION['id'] != "" || $_SESSION['login'] != "")
{
	$req = $bdd->prepare('SELECT confirm FROM users WHERE login = ? AND id_user = ?');
	$req->execute(array($_SESSION['login'], $_SESSION['id']));
	if ($req->rowCount() == 0)
	{
		header('Location: /php/logout.php');
		exit;
	}
	$verif = $req->fetch();
	if ($verif['confirm'] == 0)
	{
		header('Location: /php/logout.php');
		exit;
	}
}

function delete_folder($dir)
{
	if (file_exists($dir) && is_dir($dir))
	{
		$files = glob($dir.'/*', GLOB_MARK);
		foreach ($files as $file)
		{
			if (is_dir($file))
				delete_folder($file);
			else
				unlink($file);
		}
		rmdir($dir);
	}
}

function check_post($var)
{
	if (!isset($_POST[$var]))
		return FALSE;
	else if (empty($_POST[$var]))
		return FALSE;
	else
		return TRUE;
}

function check_get($var)
{
	if (!isset($_GET[$var]))
		return FALSE;
	else if (empty($_GET[$var]))
		return FALSE;
	else
		return TRUE;
}

?>
