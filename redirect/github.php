<?php
include_once('../config/secret.php');
include_once('OAuth_connect.php');
include_once('../php/connexion.php');

function extract_user_data($user_JSON)
{
	$user_array = json_decode($user_JSON, true);
	$name = explode(" ", $user_array['name']);
	$user = array(
		'login' => $user_array['login'],
		'email' => $user_array['email'],
		'first_name' => $name[0],
		'last_name' => $name[1],
		'image' => $user_array['avatar_url'],
	);
	return $user;
}

if (isset($_GET['code']))
{
	$url = 'https://github.com/login/oauth/access_token';
	$myvars = 'client_id='.$CLIENT_ID_GITHUB;
	$myvars .= '&client_secret='.$CLIENT_KEY_GITHUB;
	$myvars .= '&code='.$_GET['code'];
	
	
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $myvars);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	
	parse_str(curl_exec($ch), $response);

	$url2 = 'https://api.github.com/user';
	
	$ch2 = curl_init($url2);
	curl_setopt($ch2, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch2, CURLOPT_HTTPHEADER, array('Authorization: token '.$response['access_token'], 'User-Agent: gaeduron'));
	curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
	
	$response2 = curl_exec($ch2);

	$user = extract_user_data($response2);
	login_or_register($user, $bdd);
}
else
	header('Location: /signin.php');
?>
