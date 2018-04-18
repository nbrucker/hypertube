function keyUp(event, id_movie)
{
	if (event.keyCode == 13)
		addComment(id_movie);
}
function addComment(id_movie)
{
	let comment = $('#new-message').val();
	if (comment == '')
		return ;
	$.ajax(
	{
		url : '/php/addComment.php',
		type : 'POST',
		data : 'comment=' + comment + '&movie=' + id_movie,
		dataType : 'html',
		success : function(code_html, statut)
		{
			if (code_html == "error")
				return ;
			$('#message-list').prepend(code_html);
			$('#new-message').val('');
			if ($('#no-message'))
				$('#no-message').remove();
		}
	});
}
