<?php

include_once('../config/secret.php');
include_once('OAuth_connect.php');
include_once('../php/connexion.php');

function extract_user_data($user_JSON)
{
	$user_array = json_decode($user_JSON, true);
	$user = array(
		'login' => $user_array['login'],
		'email' => $user_array['email'],
		'first_name' => $user_array['first_name'],
		'last_name' => $user_array['last_name'],
		'image' => $user_array['image_url'],
	);
	return $user;
}

if (isset($_GET['code']))
{
	$url = 'https://api.intra.42.fr/oauth/token';
	$myvars = 'grant_type=authorization_code&redirect_uri=http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].'/redirect/42.php';
	$myvars .= '&client_id=99679e81eca11d015b7d8318cc286c6a8582d37cce7ab2c7e6f1134629e01061';
	$myvars .= '&client_secret='.$API_KEY_42;
	$myvars .= '&code='.$_GET['code'];
	
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $myvars);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	
	$response = json_decode(curl_exec($ch), true);
	
	$url2 = 'https://api.intra.42.fr/v2/me';
	
	$ch2 = curl_init($url2);
	curl_setopt($ch2, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch2, CURLOPT_HTTPHEADER, array('Authorization: Bearer '.$response['access_token']));
	curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
	
	$response2 = curl_exec($ch2);

	$user = extract_user_data($response2);

	login_or_register($user, $bdd);
}
else
	header('Location: /signin.php');

?>
