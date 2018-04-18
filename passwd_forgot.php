<?php
include_once('php/connexion.php');

if (isset($_POST['email']) && $_POST['email'] != "")
{

	if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
	{
		echo "<style>.alert { display: block!important; } </style>";
		$txt = $lang['passwd_forgot_invalid'];
	}
	else{
		$email = htmlspecialchars($_POST['email']);
		$cle_passwd = md5(microtime(TRUE)*100000);
		$stmt = $bdd->prepare("UPDATE users SET cle_passwd=:cle_passwd WHERE email like :email");
		$stmt->bindParam(':cle_passwd', $cle_passwd);
		$stmt->bindParam(':email', $email);
		$stmt->execute();

		
		ini_set( 'display_errors', 1 );
		error_reporting( E_ALL );

		$sujet = $lang['passwd_forgot_reset_subject'];

		$header = "From: adm@matcha.com\nMIME-Version: 1.0\nContent-Type: text/html; charset=utf-8\n";

				$message = '<html>
								<head>
									<title>'.$lang['passwd_forgot_reset_subject'].'</title>
								</head>
								<body>
									<img src="http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].'/assets/icon/favicon.png" style="width: 100px;">
									<p>'.$lang['passwd_forgot_reset_text'].'<br>http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].'/php/new_passwd.php?log='.$email.'&cle='.urlencode($cle_passwd).'<br>------------------------------------------------------------------------------------------<br>'.$lang['passwd_forgot_automatic'].'</p>
								</body>
							</html>';

		mail($email, $sujet, $message, $header);

		echo "<style>#alert_div { background-color: #568456!important;} </style>";
		$txt =  '<div id="alert_div"><p id="text_alert">'.$lang['passwd_forgot_sent'].'</p><span class="closebtn" onclick="del_alert()">&times;</span></div>';
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
</head>

<body>
<!-- ******* HEADER ***************** -->
	<header class="float_menu">
			<a href="/"><img src="assets/icon/logo.png" alt="logo" class="logo"/></a>
			<div class="float_menu_rigth">
				<a href="/"><h2><?php echo $lang['passwd_forgot_home'] ?></h2></a>
			</div>
	</header>

<!-- ******* ERROR ***************** -->
	<div id="alert" class="alert">
		<?php echo $txt;?>
	</div>


<!-- ******* FORMULAIRE ***************** -->
	<section class="page_account-forgot" id="form">
		<div class="banner" style="height: 180px;"></div>
			<!-- Form -->
			<form method="post" action="passwd_forgot.php" accept-charset="utf-8">

				<label for="email"><p><?php echo $lang['passwd_forgot_email'] ?></p></label>
				<br/>
				<input type="email" name="email" required />
				
				<p class="register"><a href="account_register.php"><?php echo $lang['passwd_forgot_register'] ?></a></p>
				<!-- SIGN IN -->
				<input type="submit" name="go_login_account" value="<?php echo $lang['passwd_forgot_send'] ?>" class="submit"/>
			</form>
			<!-- /end Form -->
	</section>

<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/main.js"></script>
</body>
</html>