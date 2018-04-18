<?php
include_once('connexion.php');

/////////////////////////////////
////////// CHANGE PROFIL
/////////////////////////////////

if (!check_post('submit'))
	exit;

if ($_POST['submit'] === "change_profil") {


if (isset($_POST['email']) && $_POST['email'] != "" 
	&& isset($_POST['login']) && $_POST['login'] != ""
	&& isset($_POST['first_name']) && $_POST['first_name'] != ""
	&& isset($_POST['last_name']) && $_POST['last_name'] != ""
	&& isset($_FILES['image']))
{
		$email = htmlspecialchars($_POST['email']);
		$login = htmlspecialchars($_POST['login']);
		$first_name = htmlspecialchars($_POST['first_name']);
		$last_name = htmlspecialchars($_POST['last_name']);
		$last_name = strtoupper($last_name);


		$req_login = $bdd->prepare('SELECT id_user FROM users WHERE login = ? AND id_user != '.$_SESSION['id'].'');
		$req_login->execute(array($login));

		$req_email = $bdd->prepare('SELECT id_user FROM users WHERE email = ? AND id_user != '.$_SESSION['id'].'');
		$req_email->execute(array($email));

		$fileName = $_FILES['image']['name'];
		$fileLoc = $_FILES['image']['tmp_name'];
		$fileType = $_FILES['image']['type'];
		$fileSize = $_FILES['image']['size'];
		$extension = pathinfo($fileName, PATHINFO_EXTENSION);
		if ((($extension != "png" || $fileType != "image/png") && ($extension != "jpg" || $fileType != "image/jpeg")) || $fileSize > 1000000)
		{
			echo '<div id="alert_div"><p id="text_alert">'.$lang['setting_image_invalid'].'</p><span class="closebtn" onclick="del_alert()">&times;</span></div>';
		}
		else if($req_login->rowCount() > 0)
		{
			echo '<div id="alert_div"><p id="text_alert">'.$lang['setting_used_login'].'</p><span class="closebtn" onclick="del_alert()">&times;</span></div>';
		}
		else if ($req_email->rowCount() > 0) {
			echo '<div id="alert_div"><p id="text_alert">'.$lang['setting_used_email'].'</p><span class="closebtn" onclick="del_alert()">&times;</span></div>';
		}
		else{
			$req = $bdd->prepare('SELECT * FROM users WHERE login = ? AND id_user = ?');
			$req->execute(array($_SESSION['login'] , $_SESSION['id']));

			if ($req->rowCount() == 1)
			{
				$value = $req->fetch();
				$path = '../data/'.md5(microtime(TRUE)*100000).'.'.$extension;
				if (!move_uploaded_file($fileLoc, $path))
				{
					echo '<div id="alert_div"><p id="text_alert">'.$lang['setting_move'].'</p><span class="closebtn" onclick="del_alert()">&times;</span></div>';
					exit;
				}
				if (substr($value['image'], 0, 1) == "." && file_exists(".".$value['image']))
					unlink(".".$value['image']);
				$stmt = $bdd->prepare("UPDATE users SET 
					email=:email, 
					login=:login, 
					first_name=:first_name,
					last_name=:last_name,
					image=:image
					WHERE id_user like :id");

				$stmt->bindParam(':id', $_SESSION['id']);
				$stmt->bindParam(':email', $email);
				$stmt->bindParam(':login', $_SESSION['login']);
				$stmt->bindParam(':first_name', $first_name);
				$stmt->bindParam(':last_name', $last_name);
				$stmt->bindParam(':image', substr($path, 1));
				$stmt->execute();
		
				echo "<style>#alert_div { background-color: #568456!important;} </style>";
				echo '<div id="alert_div"><p id="text_alert">'.$lang['setting_modified'].'</p><span class="closebtn" onclick="del_alert()">&times;</span></div>';
			}
			else{
				echo '<div id="alert_div"><p id="text_alert">'.$lang['setting_system'].'</p><span class="closebtn" onclick="del_alert()">&times;</span></div>';
			}	
		}	
}
else{
	echo '<div id="alert_div"><p id="text_alert">'.$lang['setting_incomplete'].'</p><span class="closebtn" onclick="del_alert()">&times;</span></div>';
}

}

/////////////////////////////////
////////// CHANGE PASSWD
/////////////////////////////////


if ($_POST['submit'] === "change_passwd") {

if (isset($_POST['old_password']) && $_POST['old_password'] != "" 
	&& isset($_POST['new_password']) && $_POST['new_password'] != "")
{
		$old_passwd = htmlspecialchars($_POST['old_password']);
		$new_passwd = htmlspecialchars($_POST['new_password']);
		$old_passwd = hash("whirlpool", htmlspecialchars($old_passwd));
		$new_passwd = hash("whirlpool", htmlspecialchars($new_passwd));

		if (strlen($_POST['new_password']) < 5){
			echo '<div id="alert_div"><p id="text_alert">'.$lang['setting_short'].'</p><span class="closebtn" onclick="del_alert()">&times;</span></div>';
		}
		else if (!preg_match("#[0-9]+#", $_POST['new_password'])){
			echo '<div id="alert_div"><p id="text_alert">'.$lang['setting_number'].'</p><span class="closebtn" onclick="del_alert()">&times;</span></div>';
		}
		else if (!preg_match("#[a-zA-Z]+#", $_POST['new_password'])){
			echo '<div id="alert_div"><p id="text_alert">'.$lang['setting_letter'].'</p><span class="closebtn" onclick="del_alert()">&times;</span></div>';
		}
		else{
			$req = $bdd->prepare('SELECT * FROM users WHERE login = ? AND passwd = ?');
			$req->execute(array($_SESSION['login'] , $old_passwd));

			if($req->rowCount() == 1)
			{
				$stmt = $bdd->prepare("UPDATE users SET passwd=:passwd WHERE login like :login");
				$stmt->bindParam(':login', $_SESSION['login']);
				$stmt->bindParam(':passwd', $new_passwd);
				$stmt->execute();
				echo "<style>#alert_div { background-color: #568456!important;} </style>";
				echo '<div id="alert_div"><p id="text_alert">'.$lang['setting_password'].'</p><span class="closebtn" onclick="del_alert()">&times;</span></div>';
			}
			else{
				echo '<div id="alert_div"><p id="text_alert">'.$lang['setting_wrong'].'</p><span class="closebtn" onclick="del_alert()">&times;</span></div>';
			}	
		}	
}

}



?>