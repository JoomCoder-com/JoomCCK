;(function($){
	Joomcck.loadvideo = function (id, rid, key, client, default_width)
	{
		var w = default_width;
		if(!w){
			w = parseInt($('#video-block'+key).width()) || default_width;
		}
		$.ajax({
			url: Joomcck.field_call_url,
			dataType: 'json',
			type: 'POST',
			data:{
				field_id: id,
				func:'loadVideo',
				field:'video',
				record_id: rid,
				client: client,
				width: w
			}
		}).done(function(json) {
			if(!json)
			{
				$('#htmlplayer'+key).remove();
				return;
			}
			if(!json.success)
			{
				$('#htmlplayer'+key).remove();
				alert(json.error);
				return;
			}
			if(json.result.html)
			{
				$('#htmlplayer'+key).html(json.result.html);
			}
			else
			{
				$('#htmlplayer'+key).remove();
			}
			if(json.result.js)
			{
				eval(json.result.js);
				$('#destr'+key).css('display', 'block');
			}
			else
			{
				$('#destr'+key).remove();
			}
		});
	}
}(jQuery))