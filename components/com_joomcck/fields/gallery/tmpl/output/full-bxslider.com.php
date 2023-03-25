<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

if(empty($this->value))
{
	return null;
}

JFactory::getDocument()->addScript(JUri::root(TRUE) . '/components/com_joomcck/fields/gallery/bxslider/jquery.bxslider.min.js');
JFactory::getDocument()->addStyleSheet(JUri::root(TRUE) . '/components/com_joomcck/fields/gallery/bxslider/jquery.bxslider.css');

$key = $this->id . '-' . $record->id;
$dir = JComponentHelper::getParams('com_joomcck')->get('general_upload') . DIRECTORY_SEPARATOR . $this->params->get('params.subfolder', $this->field_type) . DIRECTORY_SEPARATOR;
?>
	<style type="text/css">
		#bx-pager<?php echo $this->id; ?> {
			margin-top: -40px;
		}
		#bx-pager<?php echo $this->id; ?> a {
			margin-right: 5px;
			margin-bottom: 5px;
		}
		#bx-pager<?php echo $this->id; ?> a.active img {
			background-color: #E8D2FF;
			border-color: #4A1086;
		}
		.bx-controls-auto {
			text-align: right;
		}
	</style>
	<div id="galleria<?php echo $key?>">
		<?php
		foreach ($this->value as $picture_index => $file)
		{
			$picture = $dir . $file['fullpath'];
			$url     = CImgHelper::getThumb($picture, $this->params->get('params.full_width', 100), $this->params->get('params.full_height', 100), 'gallery' . $key, $record->user_id,
				array(
					 'mode'       => $this->params->get('params.full_mode', 6),
					 'strache'    => $this->params->get('params.full_stretch', 1),
					 'background' => $this->params->get('params.thumbs_background_color', "#000000"),
					 'quality'    => $this->params->get('params.full_quality', 80)
				));
			echo '<div class="slide"><img src="' . $url . '" title="'.htmlentities($file['title'], ENT_COMPAT, 'UTF-8').'" /></div>';
		}
		?>
	</div>

    <?php if(count($this->value) > 1): ?>
	<div id="bx-pager<?php echo $this->id; ?>">
		<?php
		foreach ($this->value as $picture_index => $file)
		{
			$picture = $dir . $file['fullpath'];
			$url     = CImgHelper::getThumb($picture, 40, 40, 'gallery' . $key, $record->user_id,
				array(
					 'mode'       => 1,
					 'strache'    => 1,
					 'background' => $this->params->get('params.thumbs_background_color', "#000000"),
					 'quality'    => 80
				));
			echo '<a data-slide-index="'.$picture_index.'" href="javascript:void(0)"><img class="img-circle img-polaroid" src="' . $url . '"></a>';
		}
		?>
	</div>
    <?php endif; ?>

	<script type="text/javascript">
		jQuery('#galleria<?php echo $key?>').bxSlider({
			mode: 'fade',
			auto: <?php echo count($this->value) > 1 ? 'true' : 'false'; ?>,
			autoControls: false,
			adaptiveHeight: true,
			pagerCustom: '#bx-pager<?php echo $this->id; ?>',
			captions: true
		});
	</script>


<?php if ($this->params->get('params.download_all', 0) == 1): ?>
	<div class="clearfix"></div>
	<a class="btn btn-success" href="<?php echo Url::task('files.download&fid=' . $this->id . '&rid=' . $record->id, 0); ?>">
		<?php echo JText::_('CDOWNLOADALL') ?>
	</a>
<?php endif; ?>