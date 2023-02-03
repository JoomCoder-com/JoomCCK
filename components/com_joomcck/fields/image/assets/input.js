/**
 * Created by Sergey on 1/9/14.
 */
;
(function($) {
	$(function() {
		$('button[data-image-field-id]').click(function() {
			var id = $(this).data('image-field-id');

			$.ajax({
				url:  Joomcck.field_call_url,
				type: 'POST',
				data: {
					field_id: id,
					func:     'deleteImage',
					file:     $(this).data('image-path')
				}
			});

			$('#jformfields' + id + 'hiddenimage').val('');
			$('#imagelib' + id).attr('src', Joomla.getOptions('system.paths').rootFull+'media/mint/blank.png');
		});

		$('select[data-image-field-id]').bind('change keyup', function() {

			var id = $(this).data('image-field-id');
			var val = $(this).val();
			if(!val) {
				val =  Joomla.getOptions('system.paths').rootFull+'media/mint/blank.png';
			}
			$('#imagelib' + id).attr('src', Joomla.getOptions('system.paths').rootFull + val);

		});
	});

	window.jInsertFieldValue = function(val, id) {
		$('#jformfields' + id + 'image').val(val);
		$('#imagelib' + id).attr('src', Joomla.getOptions('system.paths').rootFull+'/' + val);
	}
}(jQuery));
