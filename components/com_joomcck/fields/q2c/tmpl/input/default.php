<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();
echo $this->data;
?>

<script>
	(function($){
		var title = $('#jform_title').val();

		$('#qtcEnableQtcProd').change(function(){
			console.log($('#item_name').val() == '');
			if($('#item_name').val() == '') {
				$('#item_name').val(title);
			}
		});
	}(jQuery));
</script>
