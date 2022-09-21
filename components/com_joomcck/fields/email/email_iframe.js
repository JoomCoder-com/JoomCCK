function getEmailIframe(key, url)
{
	var ifrm = jQuery(document.createElement("iframe"))
	.attr({
		id: "email_frame"+key,
		src: url,
		width: "100%",
		height: "99%",
		//height: "<?php echo $params->get('params.height', 600);?>px",
		frameborder:"0"
	});

	var box = jQuery('#email_form'+key);
	box.html(ifrm).show();
}

function iframe_loaded(key, height)
{
	var box = jQuery('#email_form'+key);
	box.css('height', (height + 50) + 'px');
}
