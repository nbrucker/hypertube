<?php
include_once('connexion.php');

if (!check_post('submit'))
	exit;

if ($_POST['submit'] == "login") {

	if (isset($_POST['login']) && isset($_POST['password'])
		&& $_POST['login'] != "" && $_POST['password'] != "")
		{
			$login = htmlspecialchars($_POST['login']);
			$passwd = htmlspecialchars($_POST['password']);
			$passwd = hash("whirlpool", htmlspecialchars($passwd));
			
			$req = $bdd->prepare('SELECT id_user, confirm FROM users WHERE login = ? AND passwd = ?');
			$req->execute(array($login, $passwd));
			if($req->rowCount() == 1)
			{
				$data = $req->fetch();
				if ($data['confirm'] == 0)
				{
					echo '<div id="alert_div"><p id="text_alert">'.$lang['login_register_email'].'</p><span class="closebtn" onclick="del_alert()">&times;</span></div>';
				}
				else
				{
					$_SESSION['id'] = $data['id_user'];
					$_SESSION['login'] = $login;
					
					echo '<script>document.location.href="/"</script>';
					exit;
				}
			}
			else
			{
				echo '<div id="alert_div"><p id="text_alert">'.$lang['login_register_wrong'].'</p><span class="closebtn" onclick="del_alert()">&times;</span></div>';
			}
	}

}

else if ($_POST['submit'] === "register") {
	if (isset($_POST['login']) && $_POST['login'] != ""
	 && isset($_POST['password']) && $_POST['password'] != ""
	 && isset($_POST['password_conf']) && $_POST['password_conf'] != ""
	 && isset($_POST['email']) && $_POST['email'] != ""
	 && isset($_POST['first_name']) && $_POST['first_name'] != ""
	 && isset($_POST['last_name']) && $_POST['last_name'] != ""
	 && isset($_FILES['image']))
	{
			$fileName = $_FILES['image']['name'];
			$fileLoc = $_FILES['image']['tmp_name'];
			$fileType = $_FILES['image']['type'];
			$fileSize = $_FILES['image']['size'];
			$extension = pathinfo($fileName, PATHINFO_EXTENSION);
			if ((($extension != "png" || $fileType != "image/png") && ($extension != "jpg" || $fileType != "image/jpeg")) || $fileSize > 1000000)
			{
				echo '<div id="alert_div"><p id="text_alert">'.$lang['login_register_image'].'</p><span class="closebtn" onclick="del_alert()">&times;</span></div>';
			}
			else if ($_POST['password'] != $_POST['password_conf'])
			{
				echo '<div id="alert_div"><p id="text_alert">'.$lang['login_register_match'].'</p><span class="closebtn" onclick="del_alert()">&times;</span></div>';
			}
			else if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
			{
				echo '<div id="alert_div"><p id="text_alert">'.$lang['login_register_invalid'].'</p><span class="closebtn" onclick="del_alert()">&times;</span></div>';
			}
			else if (strlen($_POST['password']) < 5)
			{
				echo '<div id="alert_div"><p id="text_alert">'.$lang['login_register_short'].'</p><span class="closebtn" onclick="del_alert()">&times;</span></div>';
				$txt = "";
			}
			else if (!preg_match("#[0-9]+#", $_POST['password']))
			{
				echo '<div id="alert_div"><p id="text_alert">'.$lang['login_register_number'].'</p><span class="closebtn" onclick="del_alert()">&times;</span></div>';
			}
			else if (!preg_match("#[a-zA-Z]+#", $_POST['password']))
			{
				echo '<div id="alert_div"><p id="text_alert">'.$lang['login_register_letter'].'</p><span class="closebtn" onclick="del_alert()">&times;</span></div>';
			}
			else{
				$email = htmlspecialchars($_POST['email']);
				$login = htmlspecialchars($_POST['login']);
				$first_name = htmlspecialchars($_POST['first_name']);
				$last_name = htmlspecialchars($_POST['last_name']);
				$passwd = htmlspecialchars($_POST['password']);
				$passwd_conf = htmlspecialchars($_POST['password_conf']);
				$passwd = hash("whirlpool", htmlspecialchars($passwd));
				$passwd_conf = hash("whirlpool", htmlspecialchars($passwd_conf));
				$path = '../data/'.md5(microtime(TRUE)*100000).'.'.$extension;
				
				$req = $bdd->prepare('SELECT id_user FROM users WHERE login = ?');
				$req->execute(array($login));

				$req2 = $bdd->prepare('SELECT id_user FROM users WHERE email = ?');
				$req2->execute(array($email));

				if (!move_uploaded_file($fileLoc, $path))
				{
					echo '<div id="alert_div"><p id="text_alert">'.$lang['login_register_move'].'</p><span class="closebtn" onclick="del_alert()">&times;</span></div>';
				}
				else if($req->rowCount() > 0)
				{
					echo '<div id="alert_div"><p id="text_alert">'.$lang['login_register_used_login'].'</p><span class="closebtn" onclick="del_alert()">&times;</span></div>';
				}
				else if ($req2->rowCount() > 0)
				{
					echo '<div id="alert_div"><p id="text_alert">'.$lang['login_register_used_email'].'</p><span class="closebtn" onclick="del_alert()">&times;</span></div>';
				}
				else
				{
					echo "<style>#alert_div { background-color: #568456!important;} </style>";
					echo '<div id="alert_div"><p id="text_alert">'.$lang['login_register_confirm'].'</p><span class="closebtn" onclick="del_alert()">&times;</span></div>';
					
					$cle = md5(microtime(TRUE)*100000);

					$req = $bdd->prepare('INSERT INTO users (email, login, first_name, last_name, passwd, confirm, cle, image, api) VALUES (:email, :login, :first_name, :last_name, :passwd, 0, :cle, :image, 0)');
					$req->execute(array(
						'email' => $email,
						'login' => $login,
						'first_name' => $first_name,
						'last_name' => $last_name,
						'passwd' => $passwd,
						'cle' => $cle,
						'image' => substr($path, 1)
					));

					ini_set( 'display_errors', 1 );
			    	error_reporting( E_ALL );

					$sujet = $lang['login_register_activate_subject'];
					$header = "From: adm@hypertube.com\nMIME-Version: 1.0\nContent-Type: text/html; charset=utf-8\n";

					$message = '<html>
							      <head>
							       <title>'.$lang['login_register_activate_title'].'</title>
							      </head>
							      <body>
							       <p>'.$lang['login_register_activate_text'].'<br>http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].'/php/activation.php?log='.urlencode($login).'&cle='.urlencode($cle).'<br>------------------------------------------------------------------------------------------<br>'.$lang['login_register_automatic'].'</p>
							      </body>
							     </html>';

					mail($email, $sujet, $message, $header);

					exit;
			}
		}
	}
	else
		echo '<div id="alert_div"><p id="text_alert">'.$lang['login_register_missing'].'</p><span class="closebtn" onclick="del_alert()">&times;</span></div>';
}

else if ($_POST['submit'] === "forgot") {
	if (isset($_POST['email']) && $_POST['email'] != "")
	{

		if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
		{
			echo '<div id="alert_div"><p id="text_alert">'.$lang['login_register_invalid'].'</p><span class="closebtn" onclick="del_alert()">&times;</span></div>';
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

			$sujet = $lang['login_register_reset_subject'];

			$header = "From: adm@hypertube.com\nMIME-Version: 1.0\nContent-Type: text/html; charset=utf-8\n";

					$message = '<html>
							      <head>
							       <title>'.$lang['login_register_reset_title'].'</title>
							      </head>
							      <body>
							       <p>'.$lang['login_register_reset_text'].'<br>http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].'/php/new_passwd.php?log='.$email.'&cle='.urlencode($cle_passwd).'<br>------------------------------------------------------------------------------------------<br>'.$lang['login_register_automatic'].'</p>
							      </body>
							     </html>';

			mail($email, $sujet, $message, $header);

			echo "<style>#alert_div { background-color: #568456!important;} </style>";
			echo '<div id="alert_div"><p id="text_alert">'.$lang['login_register_sent'].'</p><span class="closebtn" onclick="del_alert()">&times;</span></div>';
		}
	}
}
	
?>