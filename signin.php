<?php 
include_once('php/connexion.php');
include_once('config/secret.php');

if (!isset($_GET['el']))
	$_GET['el'] = "";

if ($_SESSION['id'] != "" || $_SESSION['login'] != "")
{
	header('Location: /');
	exit;
}

if (!check_get('err'))
	$_GET['err'] = "none";

$type = "";
if ($_GET['el'] === "register")
{
	$type = '<script type="text/javascript">
				$(".template_login").hide();
				$(".template_register").show();
				$(".template_passwd_forgot").hide();
			</script>';
}
else
{
	$type = '<script type="text/javascript">
				$(".template_login").show();
				$(".template_register").hide();
				$(".template_passwd_forgot").hide();
			</script>';
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

<body onload="errMessage('<?php echo $_GET['err'] ?>', '<?php echo $lang['html'] ?>')">
	
	<?php include_once('header.php'); ?>

<!-- ******* LOGIN ***************** -->
	<section class="template_login">
		<form action="#" onsubmit="return false" accept-charset="utf-8" class="form">

			<label for="login_login"><p><?php echo $lang['signin_login'] ?></p></label>
			<br/>
			<input type="login" name="login_login" maxlength="40" required/>

			<label for="password_login"><p><?php echo $lang['signin_password'] ?></p></label>
			<br/>
			<input type="password" name="password_login" maxlength="20" required />

			<p class="forgot"><?php echo $lang['signin_forgot'] ?></p>
			<input type="submit" value="<?php echo $lang['signin_signin'] ?>" class="submit transition" onclick="login()" />
		</form>
		<div class="l-landing-button-wrapper">
			<a href="https://github.com/login/oauth/authorize?scope=read:user&client_id=<?php echo $CLIENT_ID_GITHUB ?>">
				<button class="o-button--fb o-button--large transition">
					<?php echo $lang['signin_github'] ?>
				</button>
			</a>
		</div>
		<div class="l-landing-button-wrapper">
			<a href="https://api.intra.42.fr/oauth/authorize?client_id=99679e81eca11d015b7d8318cc286c6a8582d37cce7ab2c7e6f1134629e01061&redirect_uri=http%3A%2F%2F<?php echo $_SERVER['SERVER_NAME'].'%3A'.$_SERVER['SERVER_PORT'] ?>%2Fredirect%2F42.php&response_type=code">
				<button class="o-button--42 o-button--large transition">
					<?php echo $lang['signin_42'] ?>
				</button>
			</a>
		</div>
	</section>

<!-- ******* REGISTER ***************** -->
	<section class="template_register">
		<form action="#" onsubmit="return false" accept-charset="utf-8" class="form">
			<label for="email"><p><?php echo $lang['signin_email'] ?></p></label>
			<br/>
			<input type="email" name="email" maxlength="40" required />
			
			<label for="login"><p><?php echo $lang['signin_login'] ?></p></label>
			<br/>
			<input type="login" name="login" maxlength="40" required />

			<label for="first_name"><p><?php echo $lang['signin_first_name'] ?></p></label>
			<br/>
			<input type="text" name="first_name" maxlength="40" required />
			
			<label for="last_name"><p><?php echo $lang['signin_last_name'] ?></p></label>
			<br/>
			<input type="text" name="last_name" maxlength="40" required />

			<label for="password"><p><?php echo $lang['signin_password'] ?></p></label>
			<br/>
			<input type="password" name="password" maxlength="20" minlength="5" required />
			
			<label for="password_conf"><p><?php echo $lang['signin_confirmation'] ?></p></label>
			<br/>
			<input type="password" name="password_conf" maxlength="20" minlength="5" required />

			<label for="image"><p><?php echo $lang['signin_image'] ?></p></label>
			<br/>
			<div id="image_box">
				<img id="more" src="assets/icon/more.svg" class="icon_more_img" alt="more"/>
				<img style="display: none;" id="image" src="">
			</div>
			<input onchange="upload_pic();" id="file" style="height: 0px; width: 0px;" type="file" name="image" />
			
			<input type="submit" value="<?php echo $lang['signin_create'] ?>" class="submit transition" onclick="register()" />
		</form>
	</section>

<!-- ******* PASSWD FORGOT ***************** -->
	<section class="template_passwd_forgot">
		<form action="#" onsubmit="return false" accept-charset="utf-8" class="form">

			<label for="email_forgot"><p><?php echo $lang['signin_email'] ?></p></label>
			<br/>
			<input type="email" name="email_forgot" required />

			<input type="submit" value="<?php echo $lang['signin_send'] ?>" class="submit transition" onclick="forgot_passwd()" />
		</form>
	</section>

<!-- ******* JAVASCRIPT ***************** -->
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/main.js"></script>
<script type="text/javascript">

	var image;

	window.addEventListener("dragover",function(e){
		e = e || event;
		e.preventDefault();
	},false);
	window.addEventListener("drop",function(e){
		e = e || event;
		e.preventDefault();
	},false);

	function get_pic(file)
	{
		if (file == undefined)
			return ;
		if ((file.type == "image/png" || file.type == "image/jpeg") && file.size <= 1000000)
		{
			var reader = new FileReader();
			reader.onload = function(e)
			{
				$("#image").attr('src', e.target.result);
				$("#image").css("display", "initial");
				image = file;
				$("#more").css("display", "none");
			}
			reader.readAsDataURL(file);
		}
	}

	function upload_pic(id)
	{
		var file = document.getElementById("file").files[0];
		$(".icon_more_img").hide();
		get_pic(file);
	}

	$(document).on('drop', '#image_box', function(e) 
	{
		e.preventDefault();
		e.stopPropagation();
		var file = e.originalEvent.dataTransfer.files[0];
		get_pic(file);
	});

	$("#image_box").click(function() {
		$("#file").trigger("click");
	});

	function login(){

		var formData = {
			'login'				: $('input[name=login_login]').val(),
			'password'			: $('input[name=password_login]').val(),
			'submit'			: "login"
		};

		$.ajax({
			type		: 'POST',
			url			: 'php/login_register.php',
			data		: formData,
			encode		: true,
			success		: function(data){
				$('#alert').html(data);
				$('input[name=password_login]').val('');
			}
		})
	}

	function register(){

		var data = new FormData();

		data.append('login', $('input[name=login]').val());
		data.append('password', $('input[name=password]').val());
		data.append('password_conf', $('input[name=password_conf]').val());
		data.append('email', $('input[name=email]').val());
		data.append('first_name', $('input[name=first_name]').val());
		data.append('last_name', $('input[name=last_name]').val());
		data.append('submit', 'register');
		data.append('image', image);

		$.ajax({
			type		: 'POST',
			url			: 'php/login_register.php',
			data		: data,
			processData	: false,
			contentType	: false,
			success		: function(data){
				$('#alert').html(data);
				$('input[name=password]').val('');
				$('input[name=password_conf]').val('');
			}
		})
	}

	function forgot_passwd(){

		var formData = {
			'email'				: $('input[name=email_forgot]').val(),
			'submit'			: "forgot"
		};

		$.ajax({
			type		: 'POST',
			url			: 'php/login_register.php',
			data		: formData,
			encode		: true,
			success		: function(data){
				$('#alert').html(data);
			}
		})
	}



	$(".template_login").hide();
	$(".template_register").hide();
	$(".template_passwd_forgot").hide();

	$("#login_nav").click(function(){
		$(".template_login").show();
		$(".template_register").hide();
		$(".template_passwd_forgot").hide();
	});

	$("#register_nav").click(function(){
		$(".template_register").show();
		$(".template_login").hide();
		$(".template_passwd_forgot").hide();
	});

	$(".forgot").click(function(){
		$(".template_passwd_forgot").show();
		$(".template_register").hide();
		$(".template_login").hide();
	});

</script>
<?php echo $type;?>
</body>
</html>
