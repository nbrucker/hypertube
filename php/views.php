<?php
include_once('connexion.php');

if (!check_post('id_movie') || !check_post('hash'))
	exit;

session_start();

if ($_SESSION['id'] == "" || $_SESSION['login'] == "")
	exit;

$movie = htmlspecialchars($_POST['id_movie']);
$hash = htmlspecialchars($_POST['hash']);
$id = $_SESSION['id'];

$stmt = $bdd->prepare("UPDATE hash SET 
		date=:date
		WHERE hash=:hash");

$date = time();

$stmt->bindParam(':date', $date);
$stmt->bindParam(':hash', $hash);
$stmt->execute();

$req = $bdd->prepare('SELECT * FROM views WHERE id_user = ? AND hash_movie = ? AND id_movie = ?');
$req->execute(array($id, $hash, $movie));
		
if($req->rowCount() == 0)
{
	$req = $bdd->prepare('INSERT INTO views (id_user, hash_movie, id_movie) VALUES (:id_user, :hash_movie, :id_movie)');
	$req->execute(array(
		'id_user' => $id,
		'hash_movie' => $hash,
		'id_movie' => $movie
	));
}	

?>
