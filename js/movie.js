async function setSub(path)
{
	await sleep(1000);
	var arr = JSON.parse(path);
	var video = document.getElementById("iframe");
	if (video)
		video = video.contentDocument;
	if (video)
		video = video.getElementsByName("media");
	if (video)
		video = video[0];
	if (video)
	{
		var track;
		if (arr[1] == 1)
		{
			track = "<track src='../films/" + arr[0] + "/en.vtt' kind='subtitles' srclang='en' label='English'>";
			video.insertAdjacentHTML('beforeend', track);
		}
		if (arr[2] == 1)
		{
			track = "<track src='../films/" + arr[0] + "/fr.vtt' kind='subtitles' srclang='fr' label='French'>";
			video.insertAdjacentHTML('beforeend', track);
		}
	}
}
function setVideo(hash, lang)
{
	$.ajax(
	{
		url : '/php/getPath.php',
		type : 'POST',
		data : 'hash=' + hash,
		dataType : 'html',
		success : function(code_html, statut)
		{
			if (code_html == "error")
			{
				$("#alert_div").css('background-color', '#c13c54');
				$("#alert_div").css('display', 'block');
				if (lang == "en")
					$("#text_alert").html('Error getting torrent, please try again later');
				else
					$("#text_alert").html('Erreur lors de l\'obtention du torrent, veuillez réessayer plus tard');
			}
			else
			{
				let iframe = '<iframe id="iframe" name="iframe" src="php/player.php?hash=' + hash + '"></iframe>'
				$('#player').html(iframe);
				setSub(code_html);
			}
		}
	});
}

function views(hash, id_movie)
{

	var formData = {
		'hash'		: hash,
		'id_movie'	: id_movie
	};

	$.ajax({
	   	type        : 'POST',
	   	url         : 'php/views.php',
	   	data        : formData,
	   	encode      : true,
	   	success		: function(data){

	   	}
	})
}


function getPath(hash, id_movie, lang)
{
	views(hash, id_movie);

	$.ajax(
	{
		url : '/php/getPath.php',
		type : 'POST',
		data : 'hash=' + hash,
		dataType : 'html',
		success : function(code_html, statut)
		{
			if (code_html == "error")
			{
				getTorrent(hash, id_movie, lang);
			}
			else
			{
				let iframe = '<iframe id="iframe" name="iframe" src="php/player.php?hash=' + hash + '"></iframe>'
				$('#player').html(iframe);
				setSub(code_html);
			}
		}
	});
}
async function getTorrent(hash, id_movie, lang)
{
	$.ajax(
	{
		url : '/php/startTorrent.php',
		type : 'POST',
		data : 'hash=' + hash + '&imdb=' + id_movie,
		dataType : 'html'
	});
	$("#alert_div").css('background-color', '#568456');
	$("#alert_div").css('display', 'block');
	if (lang == "en")
		$("#text_alert").html('Downloading... Please wait 30 seconds');
	else
		$("#text_alert").html('Téléchargement ... Veuillez patienter 30 secondes');
	await sleep(30000);
	setVideo(hash, lang);
}
function sleep(ms)
{
	return new Promise(resolve => setTimeout(resolve, ms));
}
