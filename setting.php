<?php include_once('php/connexion.php'); ?>
<?php
if ($_SESSION['id'] == "" || $_SESSION['login'] == "")
{
	header('Location: /');
	exit;
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

<?php 

	include_once('header.php');

	$req_form = $bdd->prepare('SELECT * FROM users WHERE id_user = ?');
	$req_form->execute(array($_SESSION['id']));
	$value = $req_form->fetch();

?>

<section  class="template_setting">
<!-- Form Profil -->
		<form action="#" onsubmit="return false" accept-charset="utf-8" class="form">

			<label for="email"><p><?php echo $lang['setting_email'] ?></p></label>
			<br/>
			<input type="email" id="email" name="email" maxlength="40" required value="<?php echo $value['email']?>" />

			<label for="login"><p><?php echo $lang['setting_login'] ?></p></label>
			<br/>
			<input type="login" id="login" name="login" maxlength="40" required value="<?php echo $value['login']?>"/>

			<label for="first_name"><p><?php echo $lang['setting_first_name'] ?></p></label>
			<br/>
			<input type="text"  id="" name="first_name" maxlength="40" required value="<?php echo $value['first_name']?>"/>
			
			<label for="last_name"><p><?php echo $lang['setting_last_name'] ?></p></label>
			<br/>
			<input type="text" name="last_name" maxlength="40" required value="<?php echo $value['last_name']?>"/>

			<label for="image"><p><?php echo $lang['setting_image'] ?></p></label>
			<br/>
			<div id="image_box">
				<img id="image" src="<?php echo $value['image'] ?>">
			</div>
			<input onchange="upload_pic();" id="file" style="height: 0px; width: 0px;" type="file" name="image" />
		
			<!-- SIGN IN -->
			<input type="submit" value="<?php echo $lang['setting_modify'] ?>" class="submit submit_setting transition" onclick="change_profil()" />
		</form>

<!-- Form Passwd-->
		<form action="#" onsubmit="return false" accept-charset="utf-8" class="form" style="margin-top: 30px; margin-bottom: 30px;">
			
			<label for="old_password"><p><?php echo $lang['setting_old'] ?></p></label>
			<br/>
			<input type="password" name="old_password" maxlength="20" required />

			<label for="new_password"><p><?php echo $lang['setting_new'] ?></p></label>
			<br/>
			<input type="password" name="new_password" maxlength="20" required />
			
			<br/>

			<p class="delete"><a href="account_delete.php"><?php echo $lang['setting_delete'] ?></a></p>

			<!-- SIGN IN -->
			<input type="submit" value="<?php echo $lang['setting_modify'] ?>" class="submit submit_passwd transition" onclick="change_passwd()"/>
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
	}, false);
	window.addEventListener("drop",function(e){
		e = e || event;
		e.preventDefault();
	}, false);

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
			}
			reader.readAsDataURL(file);
		}
	}

	function upload_pic(id)
	{
		var file = document.getElementById("file").files[0];
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

	function change_profil(){

		var data = new FormData();

		data.append('email', $('input[name=email]').val());
		data.append('login', $('input[name=login]').val());
		data.append('first_name', $('input[name=first_name]').val());
		data.append('last_name', $('input[name=last_name]').val());
		data.append('submit', 'change_profil');
		data.append('image', image);

		$.ajax({
			type		: 'POST',
			url			: 'php/setting.php',
			data		: data,
			processData	: false,
			contentType	: false,
			success		: function(data){
				$('#alert').html(data);
			}
		})
	}

	function change_passwd(){

		var formData = {
			'old_password'		: $('input[name=old_password]').val(),
			'new_password'		: $('input[name=new_password]').val(),
			'submit'			: "change_passwd"
		};

		$.ajax({
			type		: 'POST',
			url			: 'php/setting.php',
			data		: formData,
			encode		: true,
			success		: function(data){
				$('#alert').html(data);
				$('input[name=old_password]').val('');
				$('input[name=new_password]').val('');
			}
		})
	}
</script>


</body>
</html>