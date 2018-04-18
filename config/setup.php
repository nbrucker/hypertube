<?php
include_once('database.php');
try
{
	$bdd = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
	$bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$bdd->exec("SET NAMES 'UTF8'");
	$bdd->query("DROP DATABASE IF EXISTS hypertube");
	$bdd->query("CREATE DATABASE hypertube");
	$bdd->query("use hypertube");

	$bdd->query('SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";');

	$bdd->query('SET time_zone = "+00:00";');

	//users
	$bdd->query("CREATE TABLE users(
				id_user INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
				email TEXT NOT NULL,
				login TEXT NOT NULL,
				passwd TEXT NOT NULL,
				last_name TEXT NOT NULL,
				first_name TEXT NOT NULL,
				confirm BIT NOT NULL DEFAULT 0,
				cle TEXT NOT NULL,
				cle_passwd TEXT,
				image TEXT NOT NULL,
				api INT NOT NULL)");

	//comments
	$bdd->query("CREATE TABLE comments(
				id_comment INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
				id_user INT NOT NULL,
				id_movie TEXT NOT NULL,
				comment TEXT NOT NULL,
				date INT NOT NULL)");

	//genres
	$bdd->query("CREATE TABLE genres(
				id_genre INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
				genre TEXT NOT NULL)");

	$bdd->query("INSERT INTO genres (genre) VALUES
				('Action'),
				('Adventure'),
				('Animation'),
				('Biography'),
				('Comedy'),
				('Crime'),
				('Documentary'),
				('Drama'),
				('Family'),
				('Fantasy'),
				('History'),
				('Horror'),
				('Music'),
				('Musical'),
				('Mystery'),
				('Romance'),
				('Sci-Fi'),
				('Sport'),
				('Thriller'),
				('War'),
				('Western')");

	//hash
	$bdd->query("CREATE TABLE hash(
				id_hash INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
				hash TEXT NOT NULL,
				path TEXT NOT NULL,
				downloaded INT UNSIGNED NOT NULL,
				date LONG NOT NULL)");

	//views
	$bdd->query("CREATE TABLE views(
				id_views INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
				id_user INT UNSIGNED NOT NULL,
				id_movie TEXT NOT NULL,
				hash_movie TEXT NOT NULL)");

	//movies
	$bdd->query("CREATE TABLE movies(
				id_movie INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
				imdb TEXT NOT NULL,
				title TEXT NOT NULL,
				rating FLOAT NOT NULL,
				year INT NOT NULL,
				image TEXT NOT NULL,
				genres TEXT NOT NULL,
				magnet TEXT NULL,
				hash TEXT NULL)");

	$url = 'https://yts.am/api/v2/list_movies.json?limit=50&page=';
	$i = 1;
	while (1)
	{
		@$content = json_decode(file_get_contents($url.urlencode($i)), true);
		if (!$content)
		{
			header('Location: /error.php');
			exit;
		}
		if ($content["data"]["movie_count"] > 0 && $content["data"]["movies"])
		{
			foreach ($content["data"]["movies"] as $el)
			{
				$req = $bdd->prepare('SELECT id_movie FROM movies WHERE imdb = ?');
				$req->execute(array(htmlspecialchars($el['imdb_code'])));
				if ($req->rowCount() != 0)
					continue ;
				$genres = "";
				if ($el["genres"])
					$genres = ",".implode(',', $el["genres"]).",";
				$req = $bdd->prepare('INSERT INTO movies (imdb, title, rating, year, image, genres) VALUES (:imdb, :title, :rating, :year, :image, :genres)');
				$req->execute(array(
					'imdb' => htmlspecialchars($el['imdb_code']),
					'title' => htmlspecialchars($el['title']),
					'rating' => floatval(htmlspecialchars($el['rating'])),
					'year' => intval(htmlspecialchars($el['year'])),
					'image' => htmlspecialchars($el['large_cover_image']),
					'genres' => htmlspecialchars($genres)
				));
			}
			$i++;
			continue ;
		}
		else
			break ;
	}

	// second movie API
	$JSON = exec('node '.dirname(__FILE__).'/../js/scrapper.js');
	//var_dump(json_decode($JSON, true));
	$scrappedMovies = json_decode($JSON, true);
	if (count($scrappedMovies) > 0 && $scrappedMovies)
	{
		foreach ($scrappedMovies as $el)
		{
			$req = $bdd->prepare('SELECT id_movie FROM movies WHERE imdb = ?');
			$req->execute(array(htmlspecialchars($el['imdb'])));
			if ($req->rowCount() != 0)
				continue ;
			$req = $bdd->prepare('INSERT INTO movies (imdb, title, rating, year, image, genres, magnet, hash) VALUES (:imdb, :title, :rating, :year, :image, :genres, :magnet, :hash)');
			$req->execute(array(
				'imdb' => htmlspecialchars($el['imdb']),
				'title' => htmlspecialchars($el['title']),
				'rating' => floatval(htmlspecialchars($el['rating'])),
				'year' => intval(htmlspecialchars($el['year'])),
				'image' => htmlspecialchars($el['image']),
				'genres' => htmlspecialchars($el['genres']),
				'magnet' => htmlspecialchars($el['magnet']),
				'hash' => htmlspecialchars($el['hash'])
			));
		}
	}


	header('Location: /');
	exit;
}
catch (Exception $e)
{
	header('Location: /error.php');
	exit;
}
?>
