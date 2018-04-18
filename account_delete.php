<?php
include_once('php/connexion.php');

if ($_SESSION['id'] == "" || $_SESSION['login'] == "")
{
	header('Location: /');
	exit;
}

$txt = "";

$req = $bdd->prepare('SELECT api FROM users WHERE login = ? AND id_user = ?');
$req->execute(array($_SESSION['login'], $_SESSION['id']));
$user = $req->fetch();

if (isset($_POST['password']) && $_POST['password'] != "" && isset($_POST['email']) && $_POST['email'] != "" && $user['api'] == 0)
{
	$email = htmlspecialchars($_POST['email']);
	$passwd = htmlspecialchars($_POST['password']);
	$passwd = hash("whirlpool", htmlspecialchars($passwd));

	$req = $bdd->prepare('SELECT * FROM users WHERE email = ? AND passwd = ? AND id_user = ?');
	$req->execute(array($email, $passwd, $_SESSION['id']));
	if ($req->rowCount() == 1)
	{
		$data = $req->fetch();
		if (substr($data['image'], 0, 1) == "." && file_exists($data['image']))
			unlink($data['image']);
		$req2 = $bdd->prepare('DELETE FROM users WHERE id_user=:id');
		$req2->bindParam(':id', $data['id_user'], PDO::PARAM_INT);
		$req2->execute();
		$req2 = $bdd->prepare('DELETE FROM comments WHERE id_user=:id');
		$req2->bindParam(':id', $data['id_user'], PDO::PARAM_INT);
		$req2->execute();
		header('Location: php/logout.php');
	}
	else
	{
		echo "<style>#alert_div { background-color: #568456!important;} </style>";
		echo "<style>#alert_div { display: block!important;} </style>";
		$txt =  '<div id="alert_div"><p id="text_alert">'.$lang['account_delete_wrong'].'</p><span class="closebtn" onclick="del_alert()">&times;</span></div>';
	}
}
else if (isset($_POST['email']) && $_POST['email'] != "" && $user['api'] != 0)
{
	$email = htmlspecialchars($_POST['email']);

	$req = $bdd->prepare('SELECT * FROM users WHERE email = ? AND id_user = ?');
	$req->execute(array($email, $_SESSION['id']));
	if ($req->rowCount() == 1)
	{
		$data = $req->fetch();
		if (substr($data['image'], 0, 1) == "." && file_exists($data['image']))
			unlink($data['image']);
		$req2 = $bdd->prepare('DELETE FROM users WHERE id_user=:id');
		$req2->bindParam(':id', $data['id_user'], PDO::PARAM_INT);
		$req2->execute();
		$req2 = $bdd->prepare('DELETE FROM comments WHERE id_user=:id');
		$req2->bindParam(':id', $data['id_user'], PDO::PARAM_INT);
		$req2->execute();
		header('Location: php/logout.php');
	}
	else
	{
		echo "<style>#alert_div { background-color: #568456!important;} </style>";
		echo "<style>#alert_div { display: block!important;} </style>";
		$txt =  '<div id="alert_div"><p id="text_alert">'.$lang['account_delete_wrong'].'</p><span class="closebtn" onclick="del_alert()">&times;</span></div>';
	}
}

?>

<!DOCTYPE html>
<html lang="<?php echo $lang['html'] ?>">
<head>
	<?php include_once('meta.php'); ?>
		
	<!-- ******* CSS ***************** -->
	<link rel="stylesheet" type="text/css" href="css/main.css">
	<link rel="stylesheet" type="text/css" href="css/form.css">
	<link rel="stylesheet" type="text/css" href="css/responsive.css">

	<style type="text/css">
		.search_form{
			display: none;
		}
	</style>
	
</head>

<body>

<?php include_once('header.php'); ?>

<!-- ******* ALERT ***************** -->
	<div id="alert" class="alert">
		<?php echo $txt ?>
	</div>

<!-- ******* FORMULAIRE ***************** -->
	<section class="template_delete">
		<!-- Form -->
			<form action="account_delete.php" method="post" accept-charset="utf-8" class="form">

			<label for="email"><p><?php echo $lang['account_delete_email'] ?></p></label>
			<br/>
			<input type="email" name="email" maxlength="40" required />
			
			<?php
			if ($user['api'] == 0)
			{
				?>
				<label for="password"><p><?php echo $lang['account_delete_password'] ?></p></label>
				<br/>
				<input type="password" name="password" maxlength="20" required />
				<?php
			}
			?>

			<!-- SIGN IN -->
			<input type="submit" name="go_delete_account" value="<?php echo $lang['account_delete_delete'] ?>" class="submit transition" style="margin-top: 25px;" />
			</form>
			<!-- /end Form -->
	</section>


	<script type="text/javascript" src="js/jquery.js"></script>
	<script type="text/javascript" src="js/main.js"></script>
</body>
</html>