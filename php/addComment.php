<?php
include_once('connexion.php');

if ($_SESSION['id'] == "" || $_SESSION['login'] == "")
{
	echo "error";
	exit;
}

$req = $bdd->prepare('SELECT login, first_name, last_name FROM users WHERE id_user = ?');
$req->execute(array($_SESSION['id']));
if ($req->rowCount() != 1)
{
	echo "error";
	exit;
}
$data = $req->fetch();

if (check_post('comment') && check_post('movie'))
{
	$comment = htmlspecialchars($_POST['comment']);
	$movie = htmlspecialchars($_POST['movie']);
	$date = time();
	
	$req = $bdd->prepare('INSERT INTO comments (id_user, id_movie, comment, date) VALUE (:id_user, :id_movie, :comment, :date)');
	$req->execute(array(
		'id_user' => $_SESSION['id'],
		'id_movie' => $movie,
		'comment' => $comment,
		'date' => $date
	));
	?>
	<div class="message">
		<div class="message-head">
			<div class="message-head--content">
				<p class="author">
					<?php echo $data['first_name'].' '.$data['last_name'] ?>
				</p>
				<a href="./user.php?login=<?php echo $data['login'] ?>">
					<p class="login">
						@<?php echo $data['login'] ?>
					</p>
				</a>
				<p class="date">
					<?php echo date("d/m/y", $date) ?>
				</p>
			</div>
		</div>
		<p class="content">
			<?php echo $comment ?>
		</p>
	</div>
	<?php
}
else
	echo "error";
?>
