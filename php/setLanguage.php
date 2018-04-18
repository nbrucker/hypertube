<?php

include_once('connexion.php');

if (check_post('lang'))
{
	if ($_POST['lang'] == 'en' || $_POST['lang'] == 'fr')
	{
		$_SESSION["lang"] = $_POST['lang'];
		echo "ok";
	}
	else
		echo "error";
}
else
	echo "error";

?>
