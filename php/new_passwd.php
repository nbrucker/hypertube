<?php

include_once('connexion.php');

if (!check_get('log') || !check_get('cle'))
{
	header('Location: /');
	exit;
}

$email_save = $_GET['log'];
$cle_passwd = $_GET['cle'];
$cle_passwd = $cle_passwd;
$passwd = htmlspecialchars($_POST['password']);
$passwd = hash("whirlpool", $passwd);
$email = htmlspecialchars($_POST['email']);

$req = $bdd->prepare("SELECT id_user FROM users WHERE email = ? AND cle_passwd = ?");
$req->execute(array($email_save, $cle_passwd));

if($req->rowCount() == 1)
{
	$form = '<!-- ******* FORMULAIRE ***************** -->
		<section class="page_account-new">
			<!-- Form -->
			<form method="post" action="new_passwd.php" accept-charset="utf-8" class="form">

				<input type="hidden" name="email" value="'.$email_save.'">

				<label for="password"><p>'.$lang['new_passwd_password'].'</p></label>
				<br/>
				<input type="password" name="password" maxlength="20" required />

				<input type="submit" name="go_login_account" value="'.$lang['new_passwd_send'].'" class="submit"/>
			</form>
			<!-- /end Form -->
		</section>';
}

if (isset($_POST['password']) && $_POST['password'] != "" && isset($_POST['email']) && $_POST['email'] != "" )
{
	$form = '<!-- ******* FORMULAIRE ***************** -->
		<section class="page_account-new">
			<!-- Form -->
			<form method="post" action="new_passwd.php" accept-charset="utf-8" class="form">

				<input type="hidden" name="email" value="'.$_POST['email'].'">

				<label for="password"><p>'.$lang['new_passwd_password'].'</p></label>
				<br/>
				<input type="password" name="password" maxlength="20" required />

				<input type="submit" name="go_login_account" value="'.$lang['new_passwd_send'].'" class="submit"/>
			</form>
			<!-- /end Form -->
		</section>';

	if (strlen($_POST['password']) < 5)
	{
		echo "<style>.alert { display: block!important; } </style>";
		$txt = $lang['new_passwd_short'];
	}
	else if (!preg_match("#[0-9]+#", $_POST['password']))
	{
		echo "<style>.alert { display: block!important; } </style>";
		$txt = $lang['new_passwd_number'];
	}
	else if (!preg_match("#[a-zA-Z]+#", $_POST['password']))
	{
		echo "<style>.alert { display: block!important; } </style>";
		$txt = $lang['new_passwd_letter'];
	}
	else
	{
		$stmt = $bdd->prepare("UPDATE users SET passwd=:passwd WHERE email like :email");
		$stmt->bindParam(':email', $email);
		$stmt->bindParam(':passwd', $passwd);
		$stmt->execute();

		echo "<style>#alert_div { background-color: #568456!important;} </style>";
		$txt =  '<div id="alert_div"><p id="text_alert">'.$lang['new_passwd_changed'].'</p><span class="closebtn" onclick="del_alert()">&times;</span></div>';
	}
}
?>


<!DOCTYPE html>
<html lang="<?php echo $lang['html'] ?>">
<head>
	<?php include_once('../meta.php'); ?>

	<!-- ******* CSS ***************** -->
	<link rel="stylesheet" type="text/css" href="../css/main.css">
	<link rel="stylesheet" type="text/css" href="../css/form.css">
	<link rel="stylesheet" type="text/css" href="../css/new_passwd.css">

	<style type="text/css">
		.page_account-new{
			position: absolute;
			overflow: auto;
			height: 100%;
			width: 100%;
			top: 0%;
			text-align: center;
			justify-content: center;
			-webkit-justify-content: center;
			align-items: center;
			-webkit-align-items: center;
			display: -webkit-flex;
		}

		.submit{
			margin-top: 25px;
		}
	</style>
</head>
  
<body>
<!-- ******* HEADER ***************** -->
	<header class="float_menu">
		<a href="/"><img src="../assets/icon/logo.png" alt="logo" class="logo"/></a>
	</header>

<!-- ******* ERROR ***************** -->
	<div id="alert" class="alert">
		<?php echo $txt;?>
	</div>

<!-- ******* FORMULAIRE ***************** -->
	<?php echo $form; ?>

	<script type="text/javascript" src="../js/main.js"></script>
</body>
</html>