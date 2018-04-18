<?php
include_once('connexion.php');

if (!isset($_POST['submit']) || !isset($_POST['movie']) || !isset($_POST['genre']) || !isset($_POST['start']) || !check_post('rating') || !check_post('year'))
	exit;

if ($_POST['submit'] === "search")
{
	$title = htmlspecialchars($_POST['movie']);
	$genre = htmlspecialchars($_POST['genre']);
	$start = htmlspecialchars($_POST['start']);
	$rating = htmlspecialchars($_POST['rating']);
	$rating = explode(' - ', $rating);
	if (count($rating) != 2)
		exit;
	$year = htmlspecialchars($_POST['year']);
	$year = explode(' - ', $year);
	if (count($year) != 2)
		exit;

	$data = [];

	$sql = "SELECT * FROM movies WHERE";
	$sql .= " year >= ? AND year <= ?";
	$sql .= " AND rating >= ? AND rating <= ?";

	$data[] = intval($year[0]);
	$data[] = intval($year[1]);
	$data[] = intval($rating[0]);
	$data[] = intval($rating[1]);

	if (!empty($genre))
	{
		$sql .= " AND genres LIKE ?";
		$data[] = "%,".$genre.",%";
	}

	if (!empty($title))
	{
		$sql .= " AND title LIKE ? ORDER BY title ASC";
		$data[] = "%".$title."%";
	}
	else
		$sql .= " ORDER BY rating DESC";

	$sql .= " LIMIT ?, 50";
	$data[] = intval($start);

	$bdd->setAttribute(PDO::ATTR_EMULATE_PREPARES, FALSE);
	$req = $bdd->prepare($sql);
	$req->execute($data);

	$ret = [];

	$ret[0] = 0;
	$ret[1] = "";
	while ($movie = $req->fetch())
	{
		$ret[1] .= '<a href="/movie.php?id='.$movie['imdb'].'&hash='.$movie['hash'].'"><div><img src="'.$movie["image"].'" /><div class="info_movie transition">';
		$reqb = $bdd->prepare('SELECT * FROM views WHERE id_user = ? AND id_movie = ?');
		$reqb->execute(array($_SESSION['id'], $movie['imdb']));
		if ($reqb->rowCount() != 0)
		{
			$ret[1] .= '<p class="movie_views">✔</p>';
		}
		$ret[1] .= '<p>'.$movie['year'].'</p><p>'.$movie['rating'].'</p></div></div></a>';
		$ret[0]++;
	}

	if ($ret[0] == 0 && $start == 0)
		$ret[1] = '<p class="no_result">'.$lang['search_empty'].'</p>';

	echo json_encode($ret);
}

?>
