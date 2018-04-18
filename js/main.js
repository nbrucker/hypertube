function errMessage(err, lang)
{
	if (err != "none" && err != "login" && err != "email" && lang != 'fr' && lang != 'en')
		return ;
	if (err != "none")
	{
		var message = "";
		if (err == "login" && lang == "fr")
			message = "ERREUR : Pseudo déjà utilisée !";
		else if (err == "email" && lang == "fr")
			message = "ERREUR : Email déjà utilisée !";
		else if (err == "login" && lang == "en")
			message = "ERROR : Login already used !";
		else if (err == "email" && lang == "en")
			message = "ERROR : Email already used !";
		var div = '<div id="alert_div">';
		div += '<p id="text_alert">' + message + '</p>';
		div += '<span class="closebtn" onclick="del_alert()">&times;</span>';
		div += '</div>';
		$("#alert").html(div);
	}
}
function del_alert()
{
	document.getElementById("alert_div").style.display = "none";
}
function setLanguage(lang)
{
	if (lang)
	{
		$.ajax(
		{
			url : '/php/setLanguage.php',
			type : 'POST',
			data : 'lang=' + lang,
			dataType : 'html',
			success : function(code_html, statut)
			{
				if (code_html == "error")
					return ;
				location.reload();
			}
		});
	}
}


var bol = 1;
$("#cross").click(function(){
	if (bol == 1)
	{
		$('.navbar_filter').show();
		bol = 0;
	}
	else
	{
		$('.navbar_filter').hide();
		bol = 1;
	}	
});
