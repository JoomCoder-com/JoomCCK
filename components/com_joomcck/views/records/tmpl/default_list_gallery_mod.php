<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');
if(!$this->items)
{
	return;
}
$params = $this->tmpl_params['list'];
JFactory::getDocument()->addScript(JUri::root(TRUE) . '/components/com_joomcck/fields/gallery/bxslider/jquery.bxslider.min.js');
JFactory::getDocument()->addStyleSheet(JUri::root(TRUE) . '/components/com_joomcck/fields/gallery/bxslider/jquery.bxslider.css');
$picture_index = 0;
?>
<style>
	img[data-href] {
		cursor: pointer;
	}
</style>
<div class="bxslider">
	<?php foreach($this->items AS $item): ?>
		<?php
		$field = $item->fields_by_id[$params->get('tmpl_params.field_id_gallery')];
		$key   = $field->id . '-' . $item->id;
		$dir   = JComponentHelper::getParams('com_joomcck')->get('general_upload') . DIRECTORY_SEPARATOR . $field->params->get('params.subfolder', $field->field_type) . DIRECTORY_SEPARATOR;

		$pictures = array_slice($field->value, 0, $params->get('tmpl_params.pictures'));
		foreach($pictures as $file)
		{
			$picture = $dir . $file['fullpath'];
			$url     = CImgHelper::getThumb($picture, $params->get('tmpl_params.width', 100), $params->get('tmpl_params.height', 100), 'gallery' . $key, $item->user_id,
				array(
					 'mode'       => $params->get('tmpl_params.mode', 6),
					 'strache'    => $params->get('tmpl_params.stretch', 1),
					 'background' => $params->get('tmpl_params.background_color', "#000000"),
					 'quality'    => $params->get('tmpl_params.quality', 80)
				));
			echo '<div class="slide"><img title="' . htmlspecialchars($item->title, ENT_COMPAT, 'UTF-8') . '" data-href="' . JRoute::_($item->url) . '" src="' . $url . '"></div>';
			$picture_index++;
		}
		?>
	<?php endforeach; ?>
</div>
<script>
	(function($) {
		$('.bxslider').bxSlider({
			mode: '<?php echo $params->get('tmpl_params.listmode', 'vertical') ?>',
			minSlides: <?php echo $params->get('tmpl_params.frames', 5) ?>,
			maxSlides: <?php echo $params->get('tmpl_params.frames', 5) ?>,
			slideWidth: <?php echo $params->get('tmpl_params.width', 100) ?> - 10,
			slideMargin: <?php echo $params->get('tmpl_params.margin', 10) ?>,
			moveSlides: <?php echo $params->get('tmpl_params.move', 2) ?>,
			captions: <?php echo $params->get('tmpl_params.captions', 1) ?>
		});

		$('[data-href]').each(function() {
			$(this).on('click', function() {
				window.location = $(this).data('href');
			})
		});
	}(jQuery))
</script>