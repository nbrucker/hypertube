<?php 
include_once('php/connexion.php');

if ($_SESSION['id'] == "" || $_SESSION['login'] == "") {
	echo '<!-- ******* HEADER ***************** -->
	<header class="float_menu">
			<div class="background_image">
			<a href="/"><img src="assets/icon/logo.png" alt="logo" class="logo"/></a>
			</div>
			<div class="float_menu_rigth">
				<form action="#" onsubmit="return false" accept-charset="utf-8" class="search_form">

					<input type="text" name="search" maxlength="40" />

					<input type="submit" value="'.$lang['header_search'].'" class="submit_search transition" onclick="search_movie()" />
				</form>
				<a href="signin.php"><h2>'.$lang['header_signin'].'</h2></a>
				<a href="signin.php?el=register"><h2>'.$lang['header_register'].'</h2></a>
				<h2 onclick="setLanguage(\'en\')" class="lang_eng">EN</h2><h2 class="lang_barre">|</h2><h2 onclick="setLanguage(\'fr\')" class="lang_fr">FR</h2>
			</div>
	</header>

<!-- ******* ALERT ***************** -->
	<div id="alert" class="alert"></div>';

	echo '<!-- ******* NAV MOBILE ***************** -->
			<footer>
				<img src="assets/icon/search.svg" alt="search" id="cross"/>
				<a href="index.php"><img src="assets/icon/home.svg" alt="home" /></a>
				<a href="signin.php?el=register"><img src="assets/icon/register.svg" alt="register" /></a>
				<a href="signin.php"><img src="assets/icon/sign-in.svg" alt="sign-in" /></a>
			</footer>';

}
else{
	echo '<!-- ******* HEADER ***************** -->
	<header class="float_menu">
			<div class="background_image">
			<a href="/"><img src="assets/icon/logo.png" alt="logo" class="logo"/></a>
			</div>
			<div class="float_menu_rigth">
				<form action="#" onsubmit="return false" accept-charset="utf-8" class="search_form">

					<input type="text" name="search" maxlength="40" />

					<input type="submit" value="'.$lang['header_search'].'" class="submit_search transition" onclick="search_movie()" />
				</form>	

				<a href="setting.php"><img src="assets/icon/settings.svg" alt="setting"/></a>
				<a href="php/logout.php"><img src="assets/icon/logout.svg" alt="logout"/></a>
				<h2 onclick="setLanguage(\'en\')" class="lang_eng">EN</h2><h2 class="lang_barre">|</h2><h2 onclick="setLanguage(\'fr\')" class="lang_fr">FR</h2>
			</div>
	</header>

<!-- ******* ALERT ***************** -->
	<div id="alert" class="alert"></div>';

	echo '<!-- ******* NAV MOBILE ***************** -->
			<footer>
				<img src="assets/icon/search.svg" alt="search" id="cross"/>
				<a href="index.php"><img src="assets/icon/home.svg" alt="home" /></a>
				<a href="setting.php"><img src="assets/icon/settings.svg" alt="setting" /></a>
				<a href="php/logout.php"><img src="assets/icon/logout.svg" alt="logout" /></a>
			</footer>';
}

?>


