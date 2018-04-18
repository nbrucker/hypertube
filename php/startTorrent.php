<?php

include_once('connexion.php');

if (!check_post('hash') || !check_post('imdb'))
{
	echo "error";
	exit;
}

$req = $bdd->prepare('SELECT path FROM hash WHERE hash = ?');
$req->execute(array($_POST['hash']));

if ($req->rowCount() != 0)
{
	echo "error";
	exit;
}

// exec("/usr/local/Cellar/node/9.8.0/bin/node ../js/torrent.js ".$_POST['hash']." > /dev/null 2>/dev/null &");
exec("~/.brew/bin/node ../js/torrent.js ".$_POST['hash']." ".$_POST['imdb']." > /dev/null 2>/dev/null &");

?>
