$(document).ready(function() {
	$('select[name="upload_or_paste"]').on('change', function() {
		var value = $(this).val();
		if ('paste_url' == value) {
			$(this).parents('form').find('div.upload').hide()
					.find('input').removeAttr('required');
			$(this).parents('form').find('div.paste_url').show()
					.find('input').attr('required', 'required');
		} else if ('upload' == value) {
			$(this).parents('form').find('div.paste_url').hide()
					.find('input').removeAttr('required');
			$(this).parents('form').find('div.upload').show()
					.find('input').attr('required', 'required');
		}
	});
});