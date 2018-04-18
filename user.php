<?php
include_once('php/connexion.php');

if (!check_get('login'))
{
	header('Location: /');
	exit;
}

$req_form = $bdd->prepare('SELECT * FROM users WHERE login = ?');
$req_form->execute(array($_GET['login']));
$profile = $req_form->fetch();

if($req_form->rowCount() == 0)
{
	header('Location: /');
}

if (!isset($profile['image']) || $profile['image'] == '')
{
	$profile['image'] = './data/user.jpg';
}

?>
<!DOCTYPE html>
<html>
<head>
	<?php include_once('meta.php'); ?>

	<!-- ******* CSS ***************** -->
	<link rel="stylesheet" type="text/css" href="css/main.css">
	<link rel="stylesheet" type="text/css" href="css/form.css">
	<link rel="stylesheet" type="text/css" href="css/responsive.css">
	<link rel="stylesheet" type="text/css" href="css/user_profile.css">

	<script type="text/javascript" src="js/jquery.js"></script>
	<script type="text/javascript" src="js/movie.js"></script>
	<script type="text/javascript" src="js/main.js"></script>

	<style type="text/css">
		.search_form{
			display: none;
		}
	</style>

</head>
<body>
	<?php include_once('header.php'); ?>

	<div id="alert" class="alert">
		<div style="display: none;" id="alert_div">
			<p id="text_alert"></p>
			<span class="closebtn" onclick="del_alert()">&times;</span>
		</div>
	</div>

<section  class="template_setting">

	<div class="c-profile">
		<img class="c-profile-picture" src="<?php echo $profile['image']?>" alt="photo">
		<div class="c-profile-desc">
			<div class="l-profile-desc">
				<p class="c-profile-name"><?php echo $profile['first_name']?> <?php echo $profile['last_name']?></p>
				<p class="c-profile-login">@<?php echo $profile['login']?></p>
			</div>
		</div>
	</div>

</section>
</body>

</html>
