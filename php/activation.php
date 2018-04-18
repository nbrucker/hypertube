<?php

include_once('connexion.php');

if (!check_get('log') || !check_get('cle'))
{
	header('Location: /');
	exit;
}

$confirm = 0;
$login = $_GET['log'];
$cle = $_GET['cle'];

$stmt = $bdd->prepare("SELECT * FROM users WHERE login=:login ");
if ($stmt->execute(array(':login' => $login)) && $row = $stmt->fetch())
{
	$clebdd = $row['cle'];
	$confirm = $row['confirm'];
}

if ($confirm == '1')
{
	$txt = '<div id="alert_div"><p id="text_alert">'.$lang['activation_already'].'</p><span class="closebtn" onclick="del_alert()">&times;</span></div>';
}
else
{
	if ($cle == $clebdd)
	{
		echo "<style>#alert_div { background-color: #568456!important;} </style>";
		$txt = '<div id="alert_div"><p id="text_alert">'.$lang['activation_done'].'</p><span class="closebtn" onclick="del_alert()">&times;</span></div>';
		$stmt = $bdd->prepare("UPDATE users SET confirm = 1 WHERE login like :login ");
		$stmt->bindParam(':login', $login);
		$stmt->execute();
	}
	else
	{
		$txt = '<div id="alert_div"><p id="text_alert">'.$lang['activation_error'].'</p><span class="closebtn" onclick="del_alert()">&times;</span></div>';
	}
}
 
?>


<!DOCTYPE html>
<html lang="<?php echo $lang['html'] ?>">
<head>
	<?php include_once('../meta.php'); ?>

	<!-- ******* CSS ***************** -->
	<link rel="stylesheet" type="text/css" href="../css/main.css">
	<style type="text/css">
		.background_menu{
			width: 100%;
			height: 100%;
			position: relative;
			text-align: center;
			justify-content: center;
			-webkit-justify-content: center;
			align-items: center;
			-webkit-align-items: center;
			display: -webkit-flex;
			cursor: pointer;
			background-color: #141414;
		}

		.title_square{
			position: relative;
			font-size: 3.5vw;
			color: white; 
			padding: 20px;
			border: 1px solid white;
			background-color: #262626bf;
			-webkit-transition: .5s ease-out;
			-moz-transition: .5s ease-out;
			-o-transition: .5s ease-out;
			-ms-transition: .5s ease-out;
			transition: .5s ease-out;
		}
    
		.title_square a{
			color: white;
		}

		.title_square:hover{
			background-color: #f9f2f254;
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
		<?php echo $txt; ?>
	</div>

<!-- ******* BACKGROUND CONFIRMATION ***************** -->
	<section class="background_menu">
		<h1 class="title_square"><a href="../signin.php"><?php echo $lang['activation_login'] ?></a></h1>
	</section>

	<script type="text/javascript" src="../js/jquery.js"></script>
	<script type="text/javascript" src="../js/main.js"></script>
</body>
</html>