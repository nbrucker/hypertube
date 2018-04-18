<?php
include_once('connexion.php');

if ($_SESSION['id'] == "" || $_SESSION['login'] == "")
{
	header('Location: /signin.php');
	exit;
}

if (!check_get('hash'))
	exit;

$req = $bdd->prepare('SELECT path FROM hash WHERE hash = ?');
$req->execute(array($_GET['hash']));

if ($req->rowCount() != 1)
	exit;

$data = $req->fetch();

$file = "../films/".$data['path'];
$fp = @fopen($file, 'rb');
$size = filesize($file);
$length = $size;
$start = 0;
$end = $size - 1;
header('Content-type: video/mp4');
header("Accept-Ranges: 0-$length");
if (isset($_SERVER['HTTP_RANGE']))
{
	$c_start = $start;
	$c_end = $end;
	list(, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
	if (strpos($range, ',') !== false)
	{
		header('HTTP/1.1 416 Requested Range Not Satisfiable');
		header("Content-Range: bytes $start-$end/$size");
		exit;
	}
	if ($range == '-')
	{
		$c_start = $size - substr($range, 1);
	}
	else
	{
		$range = explode('-', $range);
		$c_start = $range[0];
		$c_end = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $size;
	}
	$c_end = ($c_end > $end) ? $end : $c_end;
	if ($c_start > $c_end || $c_start > $size - 1 || $c_end >= $size)
	{
		header('HTTP/1.1 416 Requested Range Not Satisfiable');
		header("Content-Range: bytes $start-$end/$size");
		exit;
	}
	$start = $c_start;
	$end = $c_end;
	$length = $end - $start + 1;
	fseek($fp, $start);
	header('HTTP/1.1 206 Partial Content');
}
header("Content-Range: bytes $start-$end/$size");
header("Content-Length: ".$length);

$buffer = 1024 * 8;
while(!feof($fp) && ($p = ftell($fp)) <= $end)
{
	if ($p + $buffer > $end)
	{
		$buffer = $end - $p + 1;
	}
	set_time_limit(0);
	echo fread($fp, $buffer);
	flush();
	$p = ftell($fp);
	$fp = @fopen($file, 'rb');
	fseek($fp, $p);
}
fclose($fp);
exit();
?>
